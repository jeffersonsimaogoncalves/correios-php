<?php

namespace FlyingLuscas\Correios\Services;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use FlyingLuscas\Correios\PackageType;
use FlyingLuscas\Correios\Service;
use FlyingLuscas\Correios\TestCase;
use GuzzleHttp\Client as HttpClient;

class FreightTest extends TestCase
{
    use ArraySubsetAsserts;
    /**
     * @var \FlyingLuscas\Correios\Services\Freight
     */
    protected $freight;

    public function setUp(): void
    {
        parent::setUp();

        $http = new HttpClient;

        $this->freight = new Freight($http);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetOrigin(): void
    {
        $this->assertInstanceOf(Freight::class, $this->freight->origin('99999-999'));
        $this->assertPayloadHas('sCepOrigem', '99999999');
    }

    /**
     * Asserts payload has a given key and value.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return self
     * @throws \Exception
     */
    protected function assertPayloadHas($key, $value = null): static
    {
        if (is_null($value)) {
            $this->assertArrayHasKey($key, $this->freight->payload());

            return $this;
        }

        self::assertArraySubset([
            $key => $value,
        ], $this->freight->payload(Service::SEDEX));

        return $this;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetDestination(): void
    {
        $this->assertInstanceOf(Freight::class, $this->freight->destination('99999-999'));
        $this->assertPayloadHas('sCepDestino', '99999999');
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetServices(): void
    {
        $sedex = Service::SEDEX;

        $this->freight->services($sedex);

        $this->assertPayloadHas('nCdServico', $sedex);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testPayloadWidth(): void
    {
        $this->freight->item(1, 10, 10, 10, 1)
            ->item(2.5, 10, 10, 10, 1)
            ->item(2, 10, 10, 10, 1);

        $this->assertPayloadHas('nVlLargura', 2.5);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testPayloadHeight(): void
    {
        $this->freight->item(10, 1, 10, 10, 1)
            ->item(10, 2.5, 10, 10, 1)
            ->item(10, 2, 10, 10, 1);

        $this->assertPayloadHas('nVlAltura', 5.5);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testPayloadLength(): void
    {
        $this->freight->item(10, 10, 1, 10, 1)
            ->item(10, 10, 2.5, 10, 1)
            ->item(10, 10, 2, 10, 1);

        $this->assertPayloadHas('nVlComprimento', 2.5);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testPayloadWeight(): void
    {
        $this->freight->item(10, 10, 10, 1, 1)
            ->item(10, 10, 10, 2.5, 1)
            ->item(10, 10, 10, 2, 1);

        $this->assertPayloadHas('nVlPeso', 5.5);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testPayloadWeightWithVolume(): void
    {
        $this->freight->item(50, 50, 50, 1, 1)
            ->item(50, 50, 50, 2.5, 1)
            ->item(50, 50, 50, 2, 1);

        $this->assertPayloadHas('nVlPeso', 62.5);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetCredentials(): void
    {
        $code = '08082650';
        $password = 'n5f9t8';

        $this->assertInstanceOf(Freight::class, $this->freight->credentials($code, $password));
        $this->assertPayloadHas('nCdEmpresa', $code)
            ->assertPayloadHas('sDsSenha', $password);
    }

    /**
     * @dataProvider packageFormatProvider
     * @param $format
     * @return void
     * @throws \Exception
     */
    public function testSetPackageFormat($format): void
    {
        $this->assertInstanceOf(Freight::class, $this->freight->package($format));
        $this->assertPayloadHas('nCdFormato', $format);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetOwnHand(): void
    {
        $this->assertInstanceOf(Freight::class, $this->freight->useOwnHand(false));
        $this->assertPayloadHas('sCdMaoPropria', 'N');

        $this->freight->useOwnHand(true);
        $this->assertPayloadHas('sCdMaoPropria', 'S');
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetDeclaredValue(): void
    {
        $value = 10.38;

        $this->assertInstanceOf(Freight::class, $this->freight->declaredValue($value));
        $this->assertPayloadHas('nVlValorDeclarado', $value);
    }

    /**
     * Provide a list of all of the packages types.
     *
     * @return array
     */
    public function packageFormatProvider(): array
    {
        return [
            [PackageType::BOX],
            [PackageType::ROLL],
            [PackageType::ENVELOPE],
        ];
    }
}
