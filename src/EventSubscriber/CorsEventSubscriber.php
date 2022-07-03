<?php

namespace App\EventSubscriber;

use App\Entity\AccessToken;
use App\Entity\RefreshToken;
use App\Exception\TokenAuthenticationException;
use App\Repository\AccessTokenRepository;
use App\Repository\RefreshTokenRepository;
use App\TokenAuthority\AccessTokenAuthority;
use App\TokenAuthority\RefreshTokenAuthority;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class CorsEventSubscriber implements EventSubscriberInterface
{
    private ParameterBagInterface $moonSilkSack;
    private AccessTokenRepository $appTokenRepo;
    private RefreshTokenRepository $refreshTokenRepo;
    private RouterInterface $router;
    private LoggerInterface $logger;

    private const ALLOWED_HEADERS = [
        'Origin',
        'X-Requested-With',
        'Content-Type',
        'Accept',
        'Authorization',
        'X-CSRF-Token',
        AccessTokenAuthority::HEADER_TOKEN,
        RefreshTokenAuthority::HEADER_TOKEN,
    ];

    public function __construct(
        ParameterBagInterface $moonSilkSack,
        AccessTokenRepository $appTokenRepo,
        RefreshTokenRepository $refreshTokenRepo,
        RouterInterface $router,
        LoggerInterface $logger,
    ) {
        $this->moonSilkSack = $moonSilkSack;
        $this->appTokenRepo = $appTokenRepo;
        $this->refreshTokenRepo = $refreshTokenRepo;
        $this->router = $router;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST  => array('onKernelRequest', 9999),
            KernelEvents::RESPONSE => array('onKernelResponse', 9999),
        ];
    }

    public function onKernelRequest(RequestEvent $event) {
        if ($this->isUnrelatedEvent($event)) {
            return;
        }

        try {
            $request = $event->getRequest();

            if ($this->isPreFlightCheck($request)) {
                $response = new Response();
                $event->setResponse($response);
            } elseif (!$this->requestMatchesToken($request)) {
                throw new TokenAuthenticationException();
            }
        } catch (Exception $e) {
            $this->logger->error($e);
            // $this->logArray([
            //     'is Preflight Check' => $this->isPreFlightCheck($request) ? 'yes' : 'no',
            //     'request path' => $request->getPathInfo(),
            //     'request matches token' => $this->requestMatchesToken($request) ? 'yes' : 'no',
            //     'referrer' => rtrim($request->server->get('HTTP_REFERER'). '/'),
            //     'token Host' => rtrim($this->getRequestToken($request)?->getHost() ?? '', '/'),
            // ]);
        }
    }

    public function onKernelResponse(ResponseEvent $event) {
        if ($this->isUnrelatedEvent($event)) {
            return;
        }

        try{
            $request = $event->getRequest();
            $allowedHost = $this->isPreFlightCheck($request)
                ? $request->server->get('HTTP_REFERER')
                : $this->deriveCorsHost($request);

            $response = $event->getResponse();
            $response->headers->set('Access-Control-Allow-Origin', rtrim($allowedHost, '/'));
            $response->headers->set('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE');
            $response->headers->set('Access-Control-Allow-Headers', join(', ', self::ALLOWED_HEADERS));
        } catch(Exception $e) {
            $this->logger->error($e);
        }
    }

    /**
     * Don't do anything if it's not the master request, or if the request is from this host.
     */
    private function isUnrelatedEvent(RequestEvent|ResponseEvent $event): bool {
        return !$event->isMasterRequest() || $this->appearsToBeThisHost($event->getRequest());
    }

    private function isPreFlightCheck(Request $request): bool
    {
        return $request->getRealMethod() === 'OPTIONS';
    }

    private function isRefreshPath(Request $request): bool
    {
        $refreshUrl = $this->router->generate('oauth.token.refresh');
        return $refreshUrl === $request->getPathInfo();
    }

    private function appearsToBeThisHost(Request $request) {
        return $request->server->get('HTTP_REFERER') === null;
    }

    private function requestMatchesToken(Request $request): bool {
        $referrer = $request->server->get('HTTP_REFERER');
        $token = $this->getRequestToken($request);

        return $token === null ? false : rtrim($token->getHost(), '/') === rtrim($referrer, '/');
    }

    private function getRequestToken(Request $request): RefreshToken|AccessToken|null {
        $refreshTokenId = $request->headers->get(RefreshTokenAuthority::HEADER_TOKEN);
        $accessTokenId = $request->headers->get(AccessTokenAuthority::HEADER_TOKEN);

        switch (true) {
            case $refreshTokenId !== null:
                return $this->refreshTokenRepo->findOneBy(['token' => $refreshTokenId]);
            case $refreshTokenId !== null:
                return $this->appTokenRepo->findOneBy(['token' => $accessTokenId]);
            default:
                return null;
        }
    }

    /**
     * Will attempt to derive the CORS host from the token.\
     * If the token is old, and is missing a host, then it'll default to the `noted.uri` global value.
     */
    private function deriveCorsHost(Request $request): string {
        $allowedHost = null;

        if ($this->isRefreshPath($request)) {
            if ($this->isPreFlightCheck($request)) {
                $allowedHost = $request->server->get('HTTP_REFERER');
            } else {
                $tokenString = $request->headers->get(RefreshTokenAuthority::HEADER_TOKEN);
                $token = $this->refreshTokenRepo->findOneBy(['token' => $tokenString]);
                $allowedHost = $token?->getHost();
            }
        } else {
            $tokenString = $request->headers->get(AccessTokenAuthority::HEADER_TOKEN);
            $token = $this->appTokenRepo->findOneBy(['token' => $tokenString]);
            $allowedHost = $token?->getHost();
        }

        return $allowedHost === null ? $this->moonSilkSack->get('noted.uri') : rtrim($allowedHost, '/');
    }

    private function logArray($array) {
        $this->logger->debug(
            implode(
                "   \n",
                array_map(
                    fn($key, $value) => "{$key}: {$value}",
                    array_keys($array),
                    $array
                )
            )
        );
    }
}
