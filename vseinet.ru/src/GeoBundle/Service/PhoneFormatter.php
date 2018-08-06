<?php 

namespace GeoBundle\Service;

use AppBundle\Container\ContainerAware;

class PhoneFormatter extends ContainerAware
{
    public function format(string $phone): ?string 
    {
        $phone = preg_replace('~\D+~', '', $phone);

        if (10 != strlen($phone)) {
            return null;
        }

        if ('9' == $phone[0]) {
            preg_match('~(\d{3})(\d{3})(\d{2})(\d{2})~', $phone, $matches);

            return "+7 ({$matches[1]}) {$matches[2]}-{$matches[3]}-{$matches[4]}";
        }

        $codes = explode("\n", file_get_contents(__DIR__.'/data/codes'));
        foreach ($codes as $code) {
            if (0 === strpos($phone, $code)) {
                $codelen = strlen($code);
                if (3 == $codelen) {
                    preg_match('~(\d{3})(\d{3})(\d{2})(\d{2})~', $phone, $matches);
                
                    return "+7 ({$matches[1]}) {$matches[2]}-{$matches[3]}-{$matches[4]}";       
                }
                if (4 == $codelen) {
                    preg_match('~(\d{4})(\d{2})(\d{2})(\d{2})~', $phone, $matches);
                
                    return "+7 ({$matches[1]}) {$matches[2]}-{$matches[3]}-{$matches[4]}";     
                }
                if (5 == $codelen) {
                    preg_match('~(\d{5})(\d{1})(\d{2})(\d{2})~', $phone, $matches);
                
                    return "+7 ({$matches[1]}) {$matches[2]}-{$matches[3]}-{$matches[4]}";     
                }
                if (6 == $codelen) {
                    preg_match('~(\d{6})(\d{2})(\d{2})~', $phone, $matches);
                
                    return "+7 ({$matches[1]}) {$matches[2]}-{$matches[3]}";     
                }
                if (7 == $codelen) {
                    preg_match('~(\d{7})(\d{1})(\d{2})~', $phone, $matches);
                
                    return "+7 ({$matches[1]}) {$matches[2]}-{$matches[3]}";     
                }
                if (8 == $codelen) {
                    preg_match('~(\d{8})(\d{2})~', $phone, $matches);
                
                    return "+7 ({$matches[1]}) {$matches[2]}";     
                }
            }
        }

        return null;
    }

    public function sort(): void
    {
        $codes = explode("\n", file_get_contents(__DIR__.'/data/codes'));
        usort($codes, function($code1, $code2) {
            return strlen($code1) < strlen($code2);
        });
        array_walk($codes, function(&$code) {
            $code = trim($code);
        });
        file_put_contents(__DIR__.'/data/codes', implode("\n", $codes));
    }
}
