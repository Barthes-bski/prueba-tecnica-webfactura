<?php

namespace App\Tests\Controller;

use App\Entity\Imagen;
use App\Repository\ImagenRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ImagenControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ImagenRepository $repository;

    private string $path = '/imagen/';


    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Imagen::class);
        // Obtén el Entity Manager u otro servicio necesario
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Galería de Imagenes');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

//        $this->client->request('GET', sprintf('%snew', $this->path));
        $this->client->request('GET', sprintf('http://localhost:%d%snew', 8000, $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Guardar Registro', [
            'imagen[titulo]' => 'Testing',
            'imagen[descripcion]' => 'Testing',
            'imagen[image_url]' => 'Testing',
            'imagen[status]' => true,
        ]);

        self::assertResponseRedirects('/imagen/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Imagen();
        $fixture->setTitulo('My Title');
        $fixture->setDescripcion('My Title');
        $fixture->setImage_url('My Title');
        $fixture->setStatus(true);

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Imagen');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Imagen();
        $fixture->setTitulo('My Title');
        $fixture->setDescripcion('My Title');
        $fixture->setImage_url('My Title');
        $fixture->setStatus('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setUpdated_at('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'imagen[titulo]' => 'Something New',
            'imagen[descripcion]' => 'Something New',
            'imagen[image_url]' => 'Something New',
            'imagen[status]' => 'Something New',
        ]);

        self::assertResponseRedirects('/imagen/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitulo());
        self::assertSame('Something New', $fixture[0]->getDescripcion());
        self::assertSame('Something New', $fixture[0]->getImage_url());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getCreated_at());
        self::assertSame('Something New', $fixture[0]->getUpdated_at());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Imagen();
        $fixture->setTitulo('My Title');
        $fixture->setDescripcion('My Title');
        $fixture->setImage_url('My Title');
        $fixture->setStatus('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setUpdated_at('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/imagen/');
    }
}
