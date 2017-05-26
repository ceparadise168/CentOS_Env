<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Form\PostType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use AppBundle\Entity\Message;

class DefaultController extends Controller
{
    /**
     * 預設歡迎頁面
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $baseDir = ['base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,];

        return $this->render('default/index.html.twig', $baseDir);
    }

    /**
     * Add New Message
     * @Route("/add", name="add")
     */
    public function createAction()
    {
        // 建立新的一筆 message
        $message = new Message();
        // 設定內容       
        $message->setUserName('eric');
        $message->setMsg('hello');
        $message->setSlug('slugger');
        //$message->setPublishedAt('2017');

        // 使用 Doctrine 提供的 getManager() 操作資料庫
        $em = $this->getDoctrine()->getManager();
        // 使用 persist將資料保存
        $em->persist($message);
        // 使用 flush寫入到資料庫
        $em->flush();

        // 回傳結果檢查用,不會render到前端
        return new Response(
                ' Saved new Message with id: ' . $message->getId()
                .' name: ' . $message->getUserName()
                .' publishedAt ' . $message->getPublishedAt());
    }

    /**
     * Show the Message
     * @Route("show", name="show")
     */
    public function showAction($id = 2)
    {
        // 使用 Doctrine 提供的 getManager() 操作 Entity，並透過 key id尋找message
        $message = $this->getDoctrine()
            ->getRepository('AppBundle:Message')
            ->find($id);

        // 不存在該筆資料就回傳錯誤，若存在就 render結果到前端
        if (!$message) {
            throw $this->createNotFoundException(
                    'No message found for id ' . $id
                    );
        } else {
            $returnArray = [
                'userName' => $message->getUserName(),
                'message' => $message->getMsg(),
                'publishedAt' => $message->getpublishedAt(),
                ];

            return $this->render('/show.html.twig', $returnArray);
        }
    }

    /**
     * Show ALL Messages
     * @Route("showall", name="showall")
     */
    public function showAllAction()
    {
        // 使用 Doctrine 提供的 getRepository() 操作 Entity
        $repository = $this->getDoctrine()->getRepository('AppBundle:Message');
        // 透過findAll()做查詢
        $Msgs = $repository->findAll();
        $returnArray = ['messages' => $Msgs, ];

        // 回傳結果
        return $this->render('/showall.html.twig',$returnArray);
    }

    /**
     * Update the Message
     * @Route("update", name="update")
     */
    public function updateAction($id = 2)
    {
        // 使用 Doctrine 提供的 getManager() 操作 Entity
        $em = $this->getDoctrine()->getManager();
        // 使用 Doctrine 提供的 getRepository() 操作 Entity，透過 key id查詢單筆 message
        $message = $em->getRepository('AppBundle:Message')->find($id);

        //不存在回傳錯誤，導向首頁
        if (!$message) {
            throw $this->createNotFoundException(
                    'No product found ofr id ' . $id
                    );
            // 暫時先關閉重新導向，前端完成後再開
            // return $this->redirectToRouter('homepage');
        }
        // 若存在則做更改
        $message->setUserName('New User name!');
        $message->setMsg('hellooooooooooo');
        // 寫入資料庫
        $em->flush();
        $returnArray = [
            'userName' => $message->getUserName(),
            'message' => $message->getMsg(),
            'publishedAt' => $message->getpublishedAt(),
            ];

        // 回傳修改結果
        return $this->render('/edit.html.twig', $returnArray);
    }

    /**
     * Delete the Message
     * @Route("delete", name="delete")
     */
    public function deleteAction($id = 3)
    {
        // 使用 Doctrine提供的 getManager() 操作 Entity
        $em = $this->getDoctrine()->getManager();
        // 用 id 去找對應的留言
        $message = $this->getDoctrine()
            ->getRepository('AppBundle:Message')
            ->find($id);

        // 找不到回傳錯誤訊息
        if (!$message) {
            throw $this->createNotFoundException('No guest found');
        }
        // 使用 DoctrineManager 中的 remove() 對留言做移除
        $em->remove($message);
        // 移除了之後還沒寫入資料庫，使用flush()寫入
        $em->flush();

        // 成功寫入移除之後回傳
        return new Response('Reomved!');
        //  return $this->redirectToRouter('homepage');
    }
}
