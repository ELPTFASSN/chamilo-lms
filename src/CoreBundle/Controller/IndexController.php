<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends BaseController
{
    /**
     * Index home page.
     *
     * @Route("/", name="home", methods={"GET", "POST"}, options={"expose"=true})
     * @Route("/login", name="login", methods={"GET", "POST"}, options={"expose"=true})
     *
     * @Route("/course/{cid}/home", name="chamilo_core_course_home")
     * @Route("/courses", name="courses", methods={"GET", "POST"}, options={"expose"=true})
     *
     * @Route("/sessions", name="sessions", methods={"GET", "POST"}, options={"expose"=true})
     * @Route("/catalog/{slug}", name="catalog", methods={"GET", "POST"}, options={"expose"=true})
     * @Route("/resources/document/{nodeId}/manager", methods={"GET"}, name="resources_filemanager")
     * @Route("/account/home", name="account", options={"expose"=true}, name="chamilo_core_account_home")
     */
    public function indexAction(): Response
    {
        return $this->render('@ChamiloCore/Index/vue.html.twig');
    }

    /**
     * Toggle the student view action.
     *
     * @Route("/toggle_student_view", methods={"GET"})
     *
     * @Security("is_granted('ROLE_TEACHER')")
     */
    public function toggleStudentViewAction(Request $request): Response
    {
        if (!api_is_allowed_to_edit(false, false, false, false)) {
            throw $this->createAccessDeniedException();
        }

        $studentView = $request->getSession()->get('studentview');
        if (empty($studentView) || 'studentview' === $studentView) {
            $content = 'teacherview';
            $request->getSession()->set('studentview', $content);
        } else {
            $content = 'studentview';
            $request->getSession()->set('studentview', $content);
        }

        return new Response($content);
    }
}
