<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Exception\ValidationException;
use GeoBundle\Entity\GeoAddress;
use GeoBundle\Entity\GeoAddressToPerson;
use AppBundle\Doctrine\DBAL\ValueObject\Point;

class CreateAddressCommandHandler extends MessageHandler
{
    const GEOCODER_API = 'https://geocode-maps.yandex.ru/1.x/?format=json&geocode=';

    public function handle(CreateAddressCommand $command)
    {
        $address = $command->address.', д '.$command->house;
        $results = [];
        
        $request = $this->container->get('httplug.message_factory')->createRequest('GET', self::GEOCODER_API.$address);
        $response = $this->container->get('httplug.client.guzzle')->sendRequest($request);
        if (200 === $response->getStatusCode()) {
            $data = json_decode($response->getBody()->getContents(), true);
            $members = $data['response']['GeoObjectCollection']['featureMember'];
            foreach ($members as $member) {
                $results[] = $this->extract($member['GeoObject']);
            }
        }

        if (!empty($results)) {
            $results = array_filter($results, function($result) {
                return !empty($result['street']);
            });
        }

        if (1 < count($results)) {
            $command->variants = ['Сохранить введенный адрес'];
            foreach ($results as $result) {
                $command->variants[] = $this->glue($result);
            }
            throw new ValidationException([
                'address' => 'Не удается распознать адрес, выберите один из вариантов',
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('user.identity')->getUser();

        if ($command->id) {
            $address = $em->getRepository(GeoAddress::class)->find($command->id);
            if (!$assress instanceof Address) {
                throw new NotFoundHttpException();
            }
            $address->setPostalCode(null);
            $address->setGeoRegionId(null);
            $address->setGeoAreaId(null);
            $address->setGeoCityId(null);
            $address->setGeoStreetId(null);
            $address->setCoordinates(null);
        } else {
            $address = new GeoAddress();
        }
        if (!empty($results)) {
            $address->setPostalCode($results[0]['postalCode']);
            $address->setGeoRegionId($results[0]['region']['id']);
            if (!empty($results[0]['area'])) {
                $address->setGeoAreaId($results[0]['area']['id']);
            }
            $address->setGeoCityId($results[0]['city']['id']);
            $address->setGeoStreetId($results[0]['street']['id']);
            if (!empty($results[0]['coordinates'])) {
                list($long, $lat) = explode(' ', $results[0]['coordinates']);
                $address->setCoordinates(new Point($long, $lat));
            }
        }
        $address->setHouse($command->house);
        $address->setBuilding($command->building);
        $address->setApartment($command->apartment);
        $address->setFloor($command->floor);
        $address->setHasLift($command->hasLift);
        $address->setAddress($command->address);
        $address->setComment($command->comment);
        
        $em->persist($address);
        $em->flush($address);

        if ($command->isMain) {
            $q = $em->createQuery("
                UPDATE GeoBundle:GeoAddressToPerson ga2p 
                SET ga2p.isMain = false
                WHERE ga2p.geoAddressId != :geoAddressId AND ga2p.personId = :personId
            ");
            $q->setParameter('geoAddressId', $address->getId());
            $q->setParameter('personId', $user->person->getId());
            $q->setParameter('isMain', $command->isMain);
        }

        $ga2p = $em->getRepository(GeoAddressToPerson::class)->findOneBy([
            'geoAddressId' => $address->getId(),
            'personId' => $user->person->getId(),
        ]);
        if (!$ga2p instanceof GeoAddressToPerson) {
            $ga2p = new GeoAddressToPerson();
            $ga2p->setGeoAddressId($address->getId());
            $ga2p->setPersonId($user->person->getId());
        }
        $ga2p->setIsMain($command->isMain);

        $em->persist($ga2p);

        $em->flush();
    }

    protected function extract($object)
    {
        $result = [
            'postalCode' => $object['metaDataProperty']['GeocoderMetaData']['Address']['postal_code'] ?? null,
            'region' => null,
            'area' => null,
            'city' => null,
            'street' => null,
            'coordinates' => $object['Point']['pos'],
        ];
        $components = $object['metaDataProperty']['GeocoderMetaData']['Address']['Components'];
        foreach ($components as $component) {
            switch ($component['kind']) {
                case 'province':
                    $result['region'] = $this->find('region', $component['name']);
                    break;

                case 'area':
                    $result['area'] = $this->find('area', $component['name'], "geo_region_id = ".$result['region']['id']);
                    break;

                case 'locality':
                    $condition = "geo_region_id = ".$result['region']['id'];
                    if (!empty($result['area'])) {
                        $condition .= " AND geo_area_id = ".$result['area']['id']; 
                    }
                    $result['city'] = $this->find('city', $component['name'], $condition);
                    break;

                case 'street':
                    $result['street'] = $this->find('street', $component['name'], "geo_city_id = ".$result['city']['id']);
                    break;
            }
        }

        return $result;
    }

    protected function find($component, $name, $condition = "1=1")
    {
        $stmt = $this->getDoctrine()->getManager()->getConnection()->prepare("
            SELECT id, name, unit, word_similarity(name, :name_s) AS sml 
            FROM geo_{$component} 
            WHERE name % :name_p AND {$condition}
            ORDER BY sml DESC 
        ");
        $stmt->execute(['name_s' => $name, 'name_p' => $name]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($results as $result) {
            if (false !== strpos($name, $result['name'])) {
                return $result;
            }
        }
        
        return null;    
    }

    protected function glue($result)
    {
        $components[] = $result['region']['name'].' '.$result['region']['unit'];
        if (!empty($result['area'])) {
            $components[] = $result['area']['name'].' '.$result['area']['unit'];
        }
        $components[] = $result['city']['name'].' '.$result['city']['unit'];
        $components[] = $result['street']['name'].' '.$result['street']['unit'];

        return implode(', ', $components); 
    }
}
