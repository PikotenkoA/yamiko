<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\ProductImage;
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
}
