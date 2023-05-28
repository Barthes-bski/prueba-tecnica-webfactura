<?php

namespace App\Repository;

use App\Entity\Imagen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Asset\Packages;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @extends ServiceEntityRepository<Imagen>
 *
 * @method Imagen|null find($id, $lockMode = null, $lockVersion = null)
 * @method Imagen|null findOneBy(array $criteria, array $orderBy = null)
 * @method Imagen[]    findAll()
 * @method Imagen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImagenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Imagen::class);
    }

    public function add(Imagen $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Imagen $entity, bool $flush = false, Security $security): void
    {
        // Obtener el usuario autenticado actual
        $user = $security->getUser();

        // Verificar el rol del usuario
        if ($user && in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            // Solo los usuarios con el rol ROLE_ADMIN pueden eliminar la imagen

            $this->getEntityManager()->remove($entity);

            if ($flush) {
                $this->getEntityManager()->flush();
            }
        } else {
            $entity->setStatus(false);
            $this->add($entity, true);
        }
    }

    public function importarArchivoCsv($csvFilePath): bool
    {

        try {
            if (($handle = fopen($csvFilePath, "r")) !== false) {
                $saltarPrimeraFila = false;
                $columnasEsperadas = ['title', 'description', 'image'];
                while (($row = fgetcsv($handle, 0, ",")) !== false) {
                    if (!$saltarPrimeraFila) {
                        // Verificar si las columnas de la primera fila coinciden con las esperadas
                        if ($row !== $columnasEsperadas) {
                            fclose($handle);
                            return false;
                        }

                        $saltarPrimeraFila = true;
                        continue;
                    }
                    // Agregar la fila a los datos
                    $imagen = new Imagen();
                    $imagen->setTitulo($row[0]);
                    $imagen->setDescripcion($row[1]);
                    $imagen->setImageUrl($row[2]);
                    $imagen->setStatus(0);

                    $this->add($imagen, true);
                }

                fclose($handle);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function imagenesHabilitadas(): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.status = :status')
            ->setParameter('status', 1)
            ->orderBy('i.titulo', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Imagen[] Returns an array of Imagen objects
     */
    public function imagenesNoHabilitadas(): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.status = :status')
            ->setParameter('status', 0)
            ->orderBy('i.titulo', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Imagen[] Returns an array of Imagen objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Imagen
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
