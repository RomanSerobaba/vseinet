<?php 

namespace ContentBundle\Bus\ColorComposite\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;
use ContentBundle\Entity\Color;
use ContentBundle\Entity\ColorComposite;

class GetFormedValueQueryHandler extends MessageHandler
{
    public function handle(GetFormedValueQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($query->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $query->categoryId));
        }

        $schema = $em->getRepository(ColorComposite::class)->getSchema($query->schemaType, $category->getGender());

        $colors = array_map(function($id) use ($em) {
            $color = $em->getRepository(Color::class)->find($id);
            if (!$color instanceof Color) {
                throw new NotFoundHttpException(sprintf('Цвет %d не найден', $id));
            }

            return $color;    
        }, array_slice($query->colorIds ?: [], 0, 4));

        return $this->formatValue($schema, $colors, $query->withPicture, $query->pictureName, $category->getGender());
    }

    public function formatValue($schema, $colors, $withPicture, $pictureName, $gender)
    {
        $formedValue = '';

        if ($schema['usedAsMain'] && empty($colors)) {
            $formedValue = $schema['name'];
        }
        elseif ($schema['usedAsAddon'] && !empty($colors)) {
            $color = array_shift($colors);
            $formedValue = $color->getNameByGender($gender);
            if ($schema['name']) {
                $formedValue .= ' '.$schema['name'];
            }
        }

        if ($schema['usedWithAddons'] && !empty($colors)) {
            $addons = array_map(function($color) {
                return $color->getNameAblative();
            }, $colors);
            $lastIndex = count($addons) - 1;
            foreach ($addons as $index => $addon) {
                if (0 == $index) {
                    $formedValue .= ' с '.$addon;
                }
                elseif ($lastIndex == $index) {
                    $formedValue .= ' и '.$addon;
                }
                else {
                    $formedValue .= ', '.$addon;
                }
            }
            if ($withPicture) {
                $formedValue .= ',';
            }       
        }

        if ($withPicture) {
            if ($formedValue) {
                $formedValue .= ' ';
            }
            $formedValue .= 'с рисунком';   
        }
        if ($pictureName) {
            if ($formedValue) {
                $formedValue .= ' ';
            }
            $formedValue .= '\\\\u00AB'.$pictureName.'\\\\u00BB';
        }

        return $formedValue;
    }
}