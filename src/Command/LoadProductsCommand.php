<?php



namespace App\Command;

use App\Entity\Attribute;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\ProductImage;
use App\Repository\AttributeRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadProductsCommand extends Command
{
    protected static $defaultName = 'app:load-products';

      /**
     * @var string
     */
    private $tempDir;

    public function __construct(EntityManagerInterface $entityManager,ParameterBagInterface $parameters)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->tempDir = $parameters->get('kernel.cache_dir') . '/images';

        if (!file_exists($this->tempDir)) {
            mkdir($this->tempDir);
        }
    }

    protected function configure()
    {
        $this
            ->setDescription('Load products from COMFY json')
            ->addArgument('url', InputArgument::OPTIONAL, 'URL for parsing products')

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $url = $input->getArgument('url');
        $content = file_get_contents($url);
        $data = json_decode($content, true);


        /**
         * var ProductRepository $repo
         */
        $repo = $this->entityManager->getRepository(Product::class);

        foreach ($data as $item) {
            $io->writeln($item ['Name']);

            $product = $repo->findOneBy(['comfyID' => $item['ItemId']]);

            if (!$product) {
                $product = new Product();
                $product->setComfyID($item ['ItemId']);
                $this->entityManager->persist($product);
            }


            $product->setName($item['Name']);
            $product->setDescription($item['Description']);
            $product->setPrice($item['Price'] * 100);
            $this->processCategories($product, $item);
            $this->processAttributes($product, $item);


            if (isset($item['PictureUrl'])) {
                $image = $product->getImages()->first();

                if (!$image) {
                    $image = new ProductImage();
                    $product->addImage($image);

                    $imageContent = file_get_contents($item['PictureUrl']);
                    $imageFileName = basename($item['PictureUrl']);
                    $imagePath = $this->tempDir . '/' . $imageFileName;
                    file_put_contents($imagePath, $imageContent);

                    $uploadedImage = new UploadedFile(
                        $imagePath,
                        $imageFileName,
                        mime_content_type($imagePath),
                        null,
                        true
                    );
                    $image->setImage($uploadedImage);
                    $this->entityManager->persist($image);

                }

            }
        }

        $this->entityManager->flush();


        $io->success('OK!');
    }

    private function processCategories(Product $product, array $item)
    {
        /**
         * @var CategoryRepository $categoryRepo
         */
        $categoryRepo = $this->entityManager->getRepository(Category::class);


        foreach ($item['CategoryIds'] as $index => $categoryId)
        {
            $category = $categoryRepo->findOneBy((['comfyId' => $categoryId]));

            if (!$category){
                $category = new Category();
                $this->entityManager->persist($category);
                $category->setName($item['CategoryNames'][$index]);
            }

            $product->addCategory($category);
        }
    }

    private function processAttributes(Product $product,array $item)
    {

        $content = file_get_contents($item['Url']);
        $dom = new DomQuery($content);


        /**@var AttributeRepository $attributeRepo */
        $attributeRepo = $this->entityManager->getRepository(Attribute::class);
        $attributeNode = $dom->find('dl.features-item__list');
        $attributes = $this->getAllAttribute();

        /**
         * @var DomQueryNodes $node
         */
        foreach ($attributeNode as $node) {
            $title = $this->cleanupString($node->find('dt.title')->text());
            $value = $this->cleanupString($node->find('dd.value .value__item')->text());

            $attribute = $attributes($title) ?? null;

            if (!$attribute) {
                $attribute = new Atribute();
                $attribute->setName($title);
                $attribute->setCategory($product->getCategories()->first());
                $this->entityManager->persist($attribute);
                $attribute[$title] = $attribute;
            }

            $attributeValues = $attribute->getValuesList();

            if (!in_array($value, $attributeValues)) {
                $attributeValues[] = $value;
                $attribute->setValuesList($attributeValues);

                $productAtributeValue = $this->getProductAttributeValue($product, $attribute);
                $productAtributeValue->setValueList($attributeValues);
            }
        }

    private function cleanupString (string $string)
    {
      $string = explode("\n", $string);
      list($string) = array_values(array_filter(array_map('trim', $string)));

      return $string;
    }

    private function getAllAttributes()
    {
        $attributes = [];

        /**@var AttributeRepository $attributeRepo */
        $attributeRepo = $this->entityManager->getRepository(Attribute::class);

        foreach ($attributeRepo->findAll() as $attribute) {
            $attributes[$attribute->getName()] = $attribute;
        }

        return $attributes;
    }
        private function getProductAttributeValue(Product $product, Attribute $attribute)
        {
            $attributeValue = null;

            foreach ($product->getAttributeValues() as $productAttributeValue){
                if ($productAttributeValue->getAttribute() === $attribute) {
                    $attributeValue = $productAttributeValue;
                }
            }

            if (!$attributeValue) {
                $attributeValue = new AtributeValue();
                $attributeValue->setAttribute($attribute);
                $this->entityManager->persist($attributeValue);
                $product->addAttributeValue($attributeValue);
            }

            return $attributeValue;
        }

        private function cleanupString(string $string)
        {
            $string = explode("\n", $string);
            list($string) = array_values(array_filter(array_map('trim', $string)));

            return $string;
        }
    }
}
