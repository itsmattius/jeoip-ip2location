<?php

namespace Jeoip\Ip2Location;

use GeoIp2\Model\Asn as AsnModel;
use GeoIp2\Model\City as CityModel;
use Jeoip\Common\Location as CommonLocation;
use Jeoip\Contracts\ICidr;

class Location extends CommonLocation
{
    public static function create(string $query, ICidr $subnet, CityModel $city, ?AsnModel $asn): self
    {
        return new self(
            $query,
            $city->country->isoCode ?? '??',
            $subnet,
            $city->country->name ?? '',
            $city->mostSpecificSubdivision->name ?? '',
            $city->city->name ?? '',
            $asn?->autonomousSystemNumber,
            $asn?->autonomousSystemOrganization,
            (float) ($city->location->latitude ?? 0.0),
            (float) ($city->location->longitude ?? 0.0),
            $city->postal->code ?? '',
            $city->location->timeZone ?? '',
        );
    }

    protected string $query;
    protected string $country;
    protected string $region;
    protected string $city;
    protected ?string $asn;
    protected ?string $asn_org;
    protected float $latitude;
    protected float $longitude;
    protected string $zipcode;
    protected string $timezone;

    public function __construct(
        string $query,
        string $countryCode,
        ICidr $subnet,
        string $country,
        string $region,
        string $city,
        ?int $asn,
        ?string $asn_org,
        float $latitude,
        float $longitude,
        string $zipcode,
        string $timezone,
    ) {
        $this->setQuery($query);
        $this->setCountryCode($countryCode);
        $this->setSubnet($subnet);
        $this->setAsn($asn);
        $this->setAsnOrg($asn_org);
        $this->setCountry($country);
        $this->setRegion($region);
        $this->setCity($city);
        $this->setLatitude($latitude);
        $this->setLongitude($longitude);
        $this->setZipcode($zipcode);
        $this->setTimezone($timezone);
    }

    public function getAsn(): ?string
    {
        return $this->asn;
    }

    public function setAsn(?int $asn): void
    {
        $this->asn = $asn;
    }

    public function getAsnOrg(): ?string
    {
        return $this->asn;
    }

    public function setAsnOrg(?string $asn_org): void
    {
        $this->asn_org = $asn_org;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): void
    {
        $this->zipcode = $zipcode;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezone): void
    {
        $this->timezone = $timezone;
    }

    public function getCountryEU(): bool
    {
        $euContries = [
            'BE', 'EL', 'LT', 'PT', 'BG', 'ES', 'LU', 'RO', 'CZ', 'FR', 'HU', 'SI', 'DK', 'HR', 'MT', 'SK', 'DE', 'IT', 'NL', 'FI', 'EE', 'CY', 'AT', 'SE', 'IE', 'LV', 'PL',
        ];

        return in_array($this->countryCode, $euContries);
    }

    /**
     * @return array{countryCode:string,subnet:string,country:string,country_eu:bool,region:string,city:string,asn:?string,asn_org:?string,latitude:float,longitude:float,zipcode:string,timezone:string}
     */
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        $data['country'] = $this->country;
        $data['country_eu'] = $this->getCountryEU();
        $data['region'] = $this->region;
        $data['city'] = $this->city;
        $data['asn'] = $this->asn;
        $data['asn_org'] = $this->asn_org;
        $data['latitude'] = $this->latitude;
        $data['longitude'] = $this->longitude;
        $data['zipcode'] = $this->zipcode;
        $data['timezone'] = $this->timezone;

        return $data;
    }
}
