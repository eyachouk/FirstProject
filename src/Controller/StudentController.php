<?php

namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
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
    #[Route('/fetch2', name: 'fetch2')]
    public function fetch2(ManagerRegistry $mr): Response
    {
        $repo=$mr->getRepository(Student::class);
        $result1=$repo->findAll();

        return $this->render('student/test.html.twig', [
            'response1' => $result1,
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
    #[Route('/dql', name: 'dql')]
public function dqlStudent(EntityManagerInterface $em , Request $request , StudentRepository $repo):Response
{
    $result=$repo->findAll();
    $req=$em->createQuery("select s from App\Entity\Student s where s.name = :n ");
    if($request->isMethod('post')){
        $value=$request->get('test');
        $req->setParameter('n' , $value);
        $result=$req->getResult();
    }
    return $this->render('student/searchStudent.html.twig',[
    'student' => $result]);
}
#[Route('/dql1', name: 'dql1')]
public function dqlStudent1(EntityManagerInterface $em , Request $request , StudentRepository $repo):Response
{
    $result=$repo->findAll();
    //$req=$em->createQuery("select s from App\Entity\Student s where s.name = :n ");
    if($request->isMethod('post')){
        $value=$request->get('test');
        
        $result=$repo->fetchStudentByName($value);
        //dd($result);
    }
    return $this->render('student/searchStudent1.html.twig',[
    'student' => $result]);
}
#[Route('/dql2', name: 'dql2')]
public function dql2(EntityManagerInterface $em):Response
{
    $req=$em->createQuery("select count(s) from App\Entity\Student s");//elle compte le nombre d'etudiants 
    $result=$req->getResult();
    dd($result);
}
#[Route('/dql3', name: 'dql3')]
public function dql3(EntityManagerInterface $em):Response
{
    $req=$em->createQuery("select s.name from App\Entity\Student s Order By s.name DESC");//tri
    $result=$req->getResult();
    dd($result);
}
#[Route('/dql4', name: 'dql4')]
public function dql4(EntityManagerInterface $em):Response
{
    $req=$em->createQuery("select s.name from App\Entity\Student s where s.classroom !='null' ");
    $result=$req->getResult();
    dd($result);
}

#[Route('/dql5', name: 'dql5')]
public function dql5(EntityManagerInterface $em):Response
{
    $req=$em->createQuery("select s.name t ,c.name from App\Entity\Student s join s.classroom c");
    $result=$req->getResult();
    dd($result);
}
#[Route('/dql6', name: 'dql6')]
public function dql6(EntityManagerInterface $em):Response
{
    $req=$em->createQuery("select s.name t , c.name from App\Entity\Student s join s.classroom c where c.name='3a40'");
    $result=$req->getResult();
    dd($result);
}
#[Route('/QB', name: 'QB')]
public function QB(StudentRepository $repo):Response
{
    $result=$repo->listQB();
    dd($result);   
}
#[Route('/QB1', name: 'QB1')]
public function QB1(StudentRepository $repo):Response
{
    $result=$repo->listQB1();
    dd($result);   
}
}
