<?php

namespace App\Controller;

use App\Entity\Agence;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncode;

class AgenceController extends AbstractController
{

    private $_em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->_em = $entityManager;
    }

    /**
     * @Route("/api/localisation", methods={"POST"})
     */
    public function localiser(Request $request) {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://koumoul.com/s/geocoder/api/v1/coords",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $request->getContent(),
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response)[0];

        $latitudeUser = $data->lat;
        $longitudeUser = $data->lon;

        $agences = $this->_em->getRepository(Agence::class)->findAll();

        $possibilite = [];

        foreach ($agences as $agence){
            $result = $this->get_distance_m($latitudeUser, $longitudeUser , $agence->getLatitude(), $agence->getLongitude());

            if( $result < 80){
                $possibilite[] = [ "nom" => $agence->getNom(), "adresse" => $agence->getAdresse(), "cp" => $agence->getCp(), "ville" => $agence->getVille() ,"distance" => round($result,2) . ' km' ];
            }
        }
        return new Response(json_encode($possibilite));

    }

    function get_distance_m($lat1, $lng1, $lat2, $lng2) {
        $earth_radius = 6378137;   // Terre = sphère de 6378km de rayon
        $rlo1 = deg2rad($lng1);
        $rla1 = deg2rad($lat1);
        $rlo2 = deg2rad($lng2);
        $rla2 = deg2rad($lat2);
        $dlo = ($rlo2 - $rlo1) / 2;
        $dla = ($rla2 - $rla1) / 2;
        $a = (sin($dla) * sin($dla)) + cos($rla1) * cos($rla2) * (sin($dlo) * sin(
                    $dlo));
        $d = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return (($earth_radius * $d) /1000);
    }


    /**
     * @Route("/api/localisation", methods={"GET"})
     */
    public function getAgences() {

        $agences = $this->_em->getRepository(Agence::class)->findAll();
        $result = [];
        foreach ($agences as $agence){
            $result[] = [ "nom" => $agence->getNom(), "adresse" => $agence->getAdresse(), "cp" => $agence->getCp(), "ville" => $agence->getVille() ];
        }
        return new Response(json_encode($result));

    }


    /**
     * @Route("/api/localisation/{id}", methods={"GET"})
     */
    public function getAgence($id) {

        $agence = $this->_em->getRepository(Agence::class)->findOneBy(['id' => $id]);
        $data = ["id" => $agence->getId(), "nom" => $agence->getNom(), "adresse" => $agence->getAdresse(), "cp" => $agence->getCp(), "ville" => $agence->getVille()];
        return new Response(json_encode($data));

    }


    /**
     * @Route("/api/localisation/adresse/{adresse}/ville/{ville}", methods={"GET"})
     */
    public function localiserChoix($adresse ,$ville) {

        $curl = curl_init();
        $adresse = str_replace('-', ' ',$adresse);

        $choix = '[{ "name" : "'.$adresse.'", "city" : "'.$ville.'" }]';


        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://koumoul.com/s/geocoder/api/v1/coords?apiKey=a832ac88-48ab-4997-94d8-58e88e86ef3d",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $choix,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $data = json_decode($response)[0];

        $latitudeUser = $data->lat;
        $longitudeUser = $data->lon;

        $agences = $this->_em->getRepository(Agence::class)->findAll();

        $possibilite = [];

        foreach ($agences as $agence){
            $result = $this->get_distance_m($latitudeUser, $longitudeUser , $agence->getLatitude(), $agence->getLongitude());

            if( $result < 80){
                $possibilite[] = [ "id" => $agence->getId(), "nom" => $agence->getNom(), "adresse" => $agence->getAdresse(), "cp" => $agence->getCp(), "ville" => $agence->getVille() ,"distance" => round($result,2) . ' km' ];
            }
        }
        return new Response(json_encode($possibilite));

    }

}