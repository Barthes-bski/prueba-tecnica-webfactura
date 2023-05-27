<?php

namespace App\Test\Controller;

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
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Imagen::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Imagen index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'imagen[titulo]' => 'Testing',
            'imagen[descripcion]' => 'Testing',
            'imagen[image_url]' => 'Testing',
            'imagen[status]' => 'Testing',
            'imagen[created_at]' => 'Testing',
            'imagen[updated_at]' => 'Testing',
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
        $fixture->setStatus('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setUpdated_at('My Title');

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
            'imagen[created_at]' => 'Something New',
            'imagen[updated_at]' => 'Something New',
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
