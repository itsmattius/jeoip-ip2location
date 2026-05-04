<?php

namespace Jeoip\Ip2Location;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Jeoip\Common\Cidr;
use Jeoip\Common\Exceptions\QueryException;
use Jeoip\Common\Exceptions\UnknownLocationException;
use Jeoip\Common\Utilities;
use Jeoip\Contracts\IGeoIPService;

class GeoIPService implements IGeoIPService
{
    public function __construct(
        protected Reader $cityReader,
        protected Reader $asnReader,
        protected ?Reader $countryReader = null,
    ) {
    }

    public function query(?string $ip = null): Location
    {
        if (null === $ip) {
            throw new QueryException('ip cannot be null', '');
        }
        if (!Utilities::isIp($ip)) {
            throw new QueryException("It's not valid ip", $ip);
        }

        try {
            $city = $this->cityReader->city($ip);
        } catch (AddressNotFoundException $e) {
            throw new UnknownLocationException($ip);
        } catch (\InvalidArgumentException $e) {
            throw new QueryException($e->getMessage(), $ip);
        }

        $asn = null;
        try {
            $asn = $this->asnReader->asn($ip);
        } catch (AddressNotFoundException $e) {
            // ASN data is optional; some IPs (e.g. reserved ranges) won't have it
        }

        $subnet = $this->subnetFromCity($ip, $city);

        return Location::create($ip, $subnet, $city, $asn);
    }

    private function subnetFromCity(string $ip, \GeoIp2\Model\City $city): Cidr
    {
        $network = $city->traits->network ?? null;
        if (null !== $network) {
            return Cidr::parse((string) $network);
        }

        return new Cidr($ip, Utilities::isIpv4($ip) ? 32 : 128);
    }
}
