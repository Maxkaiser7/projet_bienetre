<?php

namespace App\Security;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use function PHPUnit\Framework\throwException;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;
    private $router;
    public const LOGIN_ROUTE = 'app_login';
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(private UrlGeneratorInterface $urlGenerator, RouterInterface $router,EntityManagerInterface $entityManager, UtilisateurRepository $utilisateurRepository)
    {
        $this->router = $router;
        $this->utilisateurRepository = $utilisateurRepository;

    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);
        $user = $this->utilisateurRepository->findOneByEmail($email);
        if ($user->isVerified() == false){
            throw new AuthenticationException('Votre compte n\'est pas vérifié. Veuillez vérifier votre boîte de réception pour le lien de vérification.');

        }
        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $referer = $request->headers->get('referer');
        $session = $request->getSession();
        $url = $session->get('url');
        return new RedirectResponse($url);


        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:
        // return new RedirectResponse($this->urlGenerator->generate('some_route'));
        return new RedirectResponse($this->urlGenerator->generate('app_accueil'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $email = $request->request->get('email','');
        //dump($this->utilisateurRepository);die;
        $user = $this->utilisateurRepository->findOneByEmail($email);
        if ($user)
        {
            $nbr = $user->getNbEssaisInfructueux();
            $user->setNbEssaisInfructueux(++$nbr);
            if ($user->getNbEssaisInfructueux() >= 4){
                $user->setBanni(1);
                $user->setNbEssaisInfructueux(0);
            }
            $this->utilisateurRepository->save($user,true);

        }


        return new RedirectResponse($this->urlGenerator->generate('app_login', ['email' => $email]));
    }
}
