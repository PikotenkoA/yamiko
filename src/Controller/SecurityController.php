<?php
namespace App\Controller;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Service\RegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout()
    {
    }
    /**
     * @Route("/register", name="security_register")
     */
    public function register(
        Request $request,
        RegistrationService $registrationService
    ) {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $registrationService->createUser($user);
            return $this->redirectToRoute('security_register_success');
        }
        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/success", name="security_register_success")
     */
    public function registerSuccess()
    {
        return $this->render('security/register_success.html.twig');
    }

    /**
     * @Route("/register/confirm/{emailCheckCode}", name="security_confirm_email")
     */
    public function confirmEmail(User $user, RegistrationService $registrationService)
    {
        $registrationService->confirmEmail($user);

        return $this->render('security/email_confirmed.html.twig');
    }
}