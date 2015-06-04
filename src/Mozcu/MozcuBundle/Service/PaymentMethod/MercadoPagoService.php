<?php

namespace Mozcu\MozcuBundle\Service\PaymentMethod;

use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Mozcu\MozcuBundle\Entity\Album;

class MercadoPagoService
{
    private $app_id;
    
    private $secret_key;
    
    private $router;
    
    public function __construct($mercadopagoData, Router $router)
    {
        $this->app_id = $mercadopagoData['app_id'];
        $this->secret_key = $mercadopagoData['secret_key'];
        $this->router = $router;
        
        
    }
    
    public function authSeller($authorizationCode, $profileId)
    {
        $url = "/oauth/token";
        $data = [
            "grant_type" => "authorization_code",
            "client_id" => $this->app_id,
            "client_secret" => $this->secret_key,
            "code" => $authorizationCode,
            "redirect_uri" => $this->router->generate('MozcuMozcuBundle_returnFroMPAuth', [
                    'profile' => $profileId], UrlGeneratorInterface::ABSOLUTE_URL)
            ];
        
        $headers = [
            "Accept" => "application/json", 
            "Content-Type" => "application/x-www-form-urlencoded"
        ];
        
        $response = $this->post($url, $data, $headers);
        return $response['response'];
    }
    
    public function refreshToken($refreshToken)
    {
        $url = "/oauth/token";
        $data = [
            "grant_type" => "refresh_token",
            "client_id" => $this->app_id,
            "client_secret" => $this->secret_key,
            "refresh_token" => $refreshToken];
        
        $headers = [
            "Accept" => "application/json", 
            "Content-Type" => "application/x-www-form-urlencoded"
        ];
        
        $response = $this->post($url, $data, $headers);
        return $response['response'];
    }
    
    public function generateAlbumCheckout($token, Album $album, $checkoutId, $price)
    {
        $data = [
            'items' => [
                [
                    "title" => $album->getArtistName() .' - ' . $album->getName(),
                    "description" => 'mozcudotcom-' . $album->getId(),
                    "quantity"  => 1,
                    "unit_price" => floatval($price),
                    "currency_id" => "USD",
                    "picture_url" => $album->getListThumbnailUrl()
                ]
            ],
            'marketplace_fee' => 0,
            'back_urls' => [
                "success" => $this->router->generate('MozcuMozcuBundle_returnFromPayment', [
                    'service' => 'mercadopago',
                    'checkoutId' => $checkoutId], UrlGeneratorInterface::ABSOLUTE_URL),
                "failure" => $this->router->generate('MozcuMozcuBundle_albumAlbum', [
                    'username' => $album->getProfile()->getUsername(),
                    'slug' => $album->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                "pending" => $this->router->generate('MozcuMozcuBundle_returnFromPayment', [
                    'service' => 'mercadopago',
                    'checkoutId' => $checkoutId], UrlGeneratorInterface::ABSOLUTE_URL)
            ]
        ];
        
        $mp = new \MP($token);
        
        $preference = $mp->create_preference($data);
        
        return $preference['response'];        
    }
    
    private function post($url, $data, $headers)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.mercadolibre.com' . $url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        return ['status' => $statusCode, 'response' => json_decode($response, true)];
    }
}
