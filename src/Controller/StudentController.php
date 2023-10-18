<?php

namespace App\Controller;
use App\Entity\Student;
use App\Form\StudentType;
use App\Entity\Classroom;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ClassroomRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    public function index(): Response
    {
        return $this->render('student/index.html.twig', [
            'controller_name' => 'StudentController',
        ]);
    }
    #[Route('/fetch', name: 'fetch')]
    public function fetch(StudentRepository $repo): Response
    {
        $result=$repo->findAll();
        return $this->render('student/test.html.twig', [
            'response' => $result,
        ]);
    }
    #[Route('/add', name: 'add')]
    public function add(ClassroomRepository $repo,ManagerRegistry $mr): Response
    {
        $c=$repo->find('1');
        $s=new Student();
        $s->setName('eya');
        $s->setEmail('eya@gmail.com');
        $s->setAge('21');
        $s->setClassroom($c);
        $em=$mr->getManager();
        $em->persist($s);
        $em->flush();
        return $this->redirectToRoute('fetch');
    }
    #[Route('/addF', name: 'addF')]
    public function addF(ClassroomRepository $repo,ManagerRegistry $mr,Request $req): Response
    {
       
        $s=new Student();//1-Instance
        $form=$this->createForm(StudentType::class,$s);
        $form->handleRequest($req);
            if ($form->isSubmitted())
            {
                $em=$mr->getManager();//3-Persist+Flush
                $em->persist($s);
                $em->flush();
                return $this->redirectToRoute('fetch');
            }
        
        return $this->render('student/add.html.twig',['f'=>$form->createView()]);
    }
    #[Route('/remove/{name}', name: 'remove')]
    public function remove(ManagerRegistry $mr,StudentRepository $repo,$name): Response
    {
       $entite=$repo->findByNom($name);
        //$entite=$repo->find($name);
        if(!$entite)
        {
            throw $this->createNotFoundException('Aucune entité trouvée avec ce nom.');
        }
        $em=$mr->getManager();
        $em->remove($entite);
        $em->flush();
        return $this->redirectToRoute('fetch');    
    }
    #[Route('/remove1/{id}', name: 'remove')]
    public function remove1(ManagerRegistry $mr,StudentRepository $repo,$id): Response
    {
       $entite=$repo->find($id);
        //$entite=$repo->find($name);
        if(!$entite)
        {
            throw $this->createNotFoundException('Aucune entité trouvée avec ce nom.');
        }
        $em=$mr->getManager();
        $em->remove($entite);
        $em->flush();
        return $this->redirectToRoute('fetch');    
    }
    #[Route('/update/{id}', name: 'update')]
    public function update(StudentRepository $repo1,ManagerRegistry $mr,Request $req,$id): Response
    {
        $s=$repo1->find($id);
        //$s=new Student();//1-Instance
        $form=$this->createForm(StudentType::class,$s);
        $form->handleRequest($req);
            if ($form->isSubmitted())
            {
                $em=$mr->getManager();//3-Persist+Flush
                $em->persist($s);
                $em->flush();
                return $this->redirectToRoute('fetch');
            }
        
        return $this->render('student/add.html.twig',['f'=>$form->createView()]);
    }

}
