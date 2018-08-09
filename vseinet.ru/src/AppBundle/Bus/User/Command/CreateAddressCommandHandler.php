<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Exception\ValidationException;
use Doctrine\ORM\Query\ResultSetMapping;
use GeoBundle\Entity\GeoAddress;

class CreateAddressCommandHandler extends MessageHandler
{
    const GEOCODER_API = 'https://geocode-maps.yandex.ru/1.x/?format=json&geocode=';

    const COMPONENTS = [
        'province' => 'region',
        'area' => 'area',
        'locality' => 'city',
        'street' => 'street',
        'house' => 'house',
    ];

    public function handle(CreateAddressCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        if ($command->id) {
            $address = $em->getRepository(GeoAddress::class)->find($command->id);
            if (!$assress instanceof Address) {
                throw new NotFoundHttpException();
            }
        } else {
            $address = new GeoAddress();
        }

        $request = $this->container->get('httplug.message_factory')->createRequest('GET', self::GEOCODER_API.$command->address);
        $response = $this->container->get('httplug.client.guzzle')->sendRequest($request);
        if (200 === $response->getStatusCode()) {
            $data = json_decode($response->getBody()->getContents(), true);
            $meta = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'];
            $point = $meta['Point']['pos'];
            $components = $meta['metaDataProperty']['GeocoderMetaData']['Address']['Components'];
            $region = null;
            $area = null;
            $city = null;
            $street = null;
            $house = null;
            foreach ($components as $component) {
                switch ($component['kind']) {
                    case 'province':
                        $region = $this->find($em, $component, 'region');
                        break;

                    case 'area':
                        $area = $this->find($em, $component, 'area', "geo_region_id = {$region['id']}");
                        break;

                    case 'locality':
                        $condition = "geo_region_id = {$region['id']}";
                        if (null !== $area) {
                            $condition .= " AND geo_area_id = {$area['id']}"; 
                        }
                        $city = $this->find($em, $component, 'city', $condition);
                        break;

                    case 'street':
                        $street = $this->find($em, $component, 'street', "geo_city_id = {$city['id']}");
                        break;

                    case 'house':
                        $house = $component['name'];
                        break;
                }
            }
            $data = [
                'region' => $region,
                'area' => $area,
                'city' => $city,
                'street' => $street,
                'house' => $house,
                'coordinates' => $point,
            ];


            print_r($data); exit;
        }


        print_r($response->getBody()->getContents()); exit;
    }

    protected function find($em, $component, $table, $condition = "1=1")
    {
        $q = $em->createNativeQuery("
            SELECT id, name, word_similarity(name, :name_s) AS sml 
            FROM geo_{$table} 
            WHERE name % :name_p AND {$condition}
            ORDER BY sml DESC 
        ", new ResultSetMapping());
        $q->setParameter('name_s', $component['name']);
        $q->setParameter('name_p', $component['name']);
        $results = $q->getResult('ListAssocHydrator');
        foreach ($results as $result) {
            if (false !== strpos($component['name'], $result['name'])) {
                return $result;
            }
        }
        
        return null;    
    }
}
