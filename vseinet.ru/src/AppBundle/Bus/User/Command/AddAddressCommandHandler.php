<?php 

namespace AppBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Bus\Exception\ValidationException;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\GeoAddress;
use AppBundle\Entity\GeoAddressToPerson;
use AppBundle\Doctrine\DBAL\ValueObject\Point;

class AddAddressCommandHandler extends MessageHandler
{
    const GEOCODER_API = 'https://geocode-maps.yandex.ru/1.x/?format=json&geocode=';

    public function handle(AddAddressCommand $command)
    {
        $result = $this->pickup($command);
        $this->check($command, $result);

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($command->id) {
            $address = $em->getRepository(GeoAddress::class)->find($command->id);
            if (!$address instanceof GeoAddress) {
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
        if (null !== $result) {
            $address->setPostalCode($result['postalCode']);
            $address->setGeoRegionId($result['region']['id']);
            if (!empty($result['area'])) {
                $address->setGeoAreaId($result['area']['id']);
            }
            $address->setGeoCityId($result['city']['id']);
            $address->setGeoStreetId($result['street']['id']);
            if (!empty($result['coordinates'])) {
                list($long, $lat) = explode(' ', $result['coordinates']);
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
                UPDATE AppBundle:GeoAddressToPerson ga2p 
                SET ga2p.isMain = false
                WHERE ga2p.geoAddressId != :geoAddressId AND ga2p.personId = :personId
            ");
            $q->setParameter('geoAddressId', $address->getId());
            $q->setParameter('personId', $user->getPersonId());
            $q->setParameter('isMain', $command->isMain);
        }

        $ga2p = $em->getRepository(GeoAddressToPerson::class)->findOneBy([
            'geoAddressId' => $address->getId(),
            'personId' => $user->getPersonId(),
        ]);
        if (!$ga2p instanceof GeoAddressToPerson) {
            $ga2p = new GeoAddressToPerson();
            $ga2p->setGeoAddressId($address->getId());
            $ga2p->setPersonId($user->getPersonId());
        }
        $ga2p->setIsMain($command->isMain);

        $em->persist($ga2p);
        $em->flush();

        $command->id = $address->getId();
    }

    protected function pickup($command)
    {
        $address = $command->address.', д '.$command->house;
        if (null === $command->variant) {
            $request = $this->container->get('httplug.message_factory')->createRequest('GET', self::GEOCODER_API.$address);
            $response = $this->container->get('httplug.client.guzzle')->sendRequest($request);
            $results = [];
            if (200 === $response->getStatusCode()) {
                $data = json_decode($response->getBody()->getContents(), true);
                $members = $data['response']['GeoObjectCollection']['featureMember'];
                foreach ($members as $member) {
                    $results[] = $this->extract($member['GeoObject']);
                }
            }

            if (!empty($results)) {
                $results = array_values(array_filter($results, function($result) {
                    return !empty($result['street']);
                }));
    
                if (1 < count($results)) {
                    $command->variants = ['Сохранить введенный адрес'];
                    foreach ($results as $result) {
                        $command->variants[] = $this->glue($result);
                    }
                    $this->get('session')->set('address-results', $results);
                    throw new ValidationException([
                        'address' => 'Не удается распознать адрес, выберите один из вариантов',
                    ]);
                }
    
                return $results[0];
            }
        } elseif ($command->variant) {
            $results = $this->get('session')->get('address-results');
            
            return $results[$command->variant - 1]; 
        }

        return null;
    }

    protected function extract($object)
    {
        $result = [
            'postalCode' => $object['metaDataProperty']['GeocoderMetaData']['Address']['postal_code'] ?? null,
            'region' => null,
            'area' => null,
            'city' => null,
            'street' => null,
            'coordinates' => $object['Point']['pos'] ?? null,
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

        if (!empty($results)) {
            $candidates = array_filter($results, function($result) {
                return 1 === intval($result['sml']);
            });
            if (!empty($candidates)) {
                if (1 === count($candidates)) {
                    return $candidates[0];
                }

                usort($candidates, function($result1, $result2) {
                    return strlen($result1['name']) > strlen($result2['name']) ? -1 : 1;
                });
                
                return $candidates[0];
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

    protected function check($command, $result)
    {
        $criteria[] = "ga2p.personId = :personId";
        $parameters['personId'] = $this->getUser()->getPersonId();

        if ($command->id) {
            $criteria[] = "a.id != :id";
            $parameters['id'] = $command->id;
        }

        if (null !== $result) {
            $criteria[] = "a.geoRegionId = :geoRegionId";
            $parameters['geoRegionId'] = $result['region']['id'];
            if (!empty($result['area'])) {
                $criteria[] = "a.geoAreaId = :geoAreaId";
                $parameters['geoAreaId'] = $result['area']['id'];
            } else {
                $criteria[] = "a.geoAreaId IS NULL";
            }
            $criteria[] = "a.geoCityId = :geoCityId";
            $parameters['geoCityId'] = $result['city']['id'];
            $criteria[] = "a.geoStreetId = :geoStreetId";
            $parameters['geoStreetId'] = $result['street']['id'];
        } else {
            $criteria[] = "a.address = :address";
            $parameters['address'] = $command->address;
        } 
        $criteria[] = "a.house = :house";
        $parameters['house'] = $command->house;
        if ($command->building) {
            $criteria[] = "a.building = :building";
            $parameters['building'] = $command->building; 
        } else {
            $criteria[] = "a.building IS NULL";
        }
        if ($command->apartment) {
            $criteria[] = "a.apartment = :apartment";
            $parameters['apartment'] = $command->apartment;
        } else {
            $criteria[] = "a.apartment IS NULL";
        }
        if ($command->floor) {
            $criteria[] = "a.floor = :floor";
            $parameters['floor'] = $command->floor;
        } else {
            $criteria[] = "a.floor IS NULL";
        }

        $where = implode(' AND ', $criteria);

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 1 
            FROM AppBundle:GeoAddress AS a 
            INNER JOIN AppBundle:GeoAddressToPerson AS ga2p WITH ga2p.geoAddressId = a.id 
            WHERE {$where}
        ");
        $q->setParameters($parameters);
        try {
            $q->getSingleScalarResult();
            throw new ValidationException([
                'address' => 'Вы уже добавили такой адрес',
            ]);
        } catch (NoResultException $e) {
        }
    }
}
