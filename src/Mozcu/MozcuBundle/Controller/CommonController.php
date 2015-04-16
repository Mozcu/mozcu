<?php

namespace Mozcu\MozcuBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class CommonController extends MozcuController {
    
    
    public function getCountriesAction(Request $request) {
        $name = $request->get('term');
        $countries = $this->getRepository('MozcuMozcuBundle:Country')->findByLikeName($name, true);
        
        $export = [];
        foreach($countries as $c) {
            $export[] = array('id' => $c->getId(), 'label' => $c->getName(), 'value' => $c->getName());
        }
        
        return $this->getJSONResponse($export);
    }
    
    public function getCitiesAction(Request $request) {
        $term = $request->get('term');
        $cities = $this->getRepository('MozcuMozcuBundle:Profile')->findCitiesByLike($term, true);
        
        $export = [];
        foreach($cities as $c) {
            $export[] = array('id' => $c['city'], 'label' => $c['city'], 'value' => $c['city']);
        }
        
        return $this->getJSONResponse($export);
    }
}
