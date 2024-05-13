<?php

namespace App\Test\Controller;

use App\Entity\Seance;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SeanceControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/seance/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Seance::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Seance index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'seance[heureDebut]' => 'Testing',
            'seance[date]' => 'Testing',
            'seance[heureFin]' => 'Testing',
            'seance[film]' => 'Testing',
            'seance[salle]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Seance();
        $fixture->setHeureDebut('My Title');
        $fixture->setDate('My Title');
        $fixture->setHeureFin('My Title');
        $fixture->setFilm('My Title');
        $fixture->setSalle('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Seance');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Seance();
        $fixture->setHeureDebut('Value');
        $fixture->setDate('Value');
        $fixture->setHeureFin('Value');
        $fixture->setFilm('Value');
        $fixture->setSalle('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'seance[heureDebut]' => 'Something New',
            'seance[date]' => 'Something New',
            'seance[heureFin]' => 'Something New',
            'seance[film]' => 'Something New',
            'seance[salle]' => 'Something New',
        ]);

        self::assertResponseRedirects('/seance/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getHeureDebut());
        self::assertSame('Something New', $fixture[0]->getDate());
        self::assertSame('Something New', $fixture[0]->getHeureFin());
        self::assertSame('Something New', $fixture[0]->getFilm());
        self::assertSame('Something New', $fixture[0]->getSalle());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Seance();
        $fixture->setHeureDebut('Value');
        $fixture->setDate('Value');
        $fixture->setHeureFin('Value');
        $fixture->setFilm('Value');
        $fixture->setSalle('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/seance/');
        self::assertSame(0, $this->repository->count([]));
    }
}
