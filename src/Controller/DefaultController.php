<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
class DefaultController extends AbstractController

{
  /**
   * @Route("/", name="index")
   */
  public function index(Request $request)
  {
      if($request->isMethod('GET')){

        $difficult = $request->get('difficulte');
        $glace = $request->get('glace');
        $roche = $request->get('roche');
        $sable = $request->get('sable');
        $minerai = $request->get('minerai');
        $argile = $request->get('argile');
        $fer = $request->get('fer');
        $inconnu = $request->get('inconnu');

        // dump($glace, $roche, $sable, $minerai, $argile, $fer, $inconnu);
        
        

      }
    
    return $this->render('index.html.twig', [
        'controller_name' => 'DefaultController',
    ]);
  }
}