<?php

namespace App\Controller;

use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonDecode;


class StockController
{
    private $_em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->_em = $em;
    }

    /**
     * @Route("/api/stock", methods={"GET"})
     */
    public function getStocks() {

        $stocks = $this->_em->getRepository(Stock::class)->findAll();
        $result = [];
        foreach ($stocks as $stock){
            $result[] = [ "id" => $stock->getId(), "articleId" => $stock->getArticleId(), "qty" => $stock->getQty()];
        }
        return new Response(json_encode($result));

    }

    /**
     * @Route("/api/stock/article/{id}", methods={"POST"})
     */
    public function postStock($id){
        $stock = $this->_em->getRepository(Stock::class)->findOneBy(['articleId' => $id]);
        if($stock->getQty() != 0) {
            $stock->setQty($stock->getQty() - 1);
            $this->_em->persist($stock);
            $this->_em->flush();
            $result = ["id" => $stock->getId(), "articleId" => $stock->getArticleId(), "qty" => $stock->getQty()];
            return new Response(json_encode($result));
        }else {
            return new Response(null);
        }

    }

    /**
     * @Route("/api/stock/commande", methods={"POST"})
     */
    public function commandStock(Request $request){
        $data = json_decode($request->getContent());
        foreach ($data as $commande){
            $stock = $this->_em->getRepository(Stock::class)->findOneBy(['articleId' => $commande->IdArticle]);
            if($stock->getQty() >= $commande->Qty) {
                $stock->setQty($stock->getQty() - $commande->Qty);
                $this->_em->persist($stock);
            }else {
                return new Response(null,Response::HTTP_BAD_REQUEST);
            }
        }
        $this->_em->flush();
        return new Response('ok');
    }

    /**
     * @Route("/api/stock/{id}", methods={"GET"})
     */
    public function getStock($id){
        $stock = $this->_em->getRepository(Stock::class)->findOneBy(['articleId' => $id]);
        $result = [ "id" => $stock->getId(), "articleId" => $stock->getArticleId(), "qty" => $stock->getQty()];
        return new Response(json_encode($result));
    }

}