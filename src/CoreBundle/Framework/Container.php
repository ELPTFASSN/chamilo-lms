<?php

declare(strict_types=1);

/* For licensing terms, see /license.txt */

namespace Chamilo\CoreBundle\Framework;

use Chamilo\CoreBundle\Component\Editor\Editor;
use Chamilo\CoreBundle\Repository\AssetRepository;
use Chamilo\CoreBundle\Repository\CourseCategoryRepository;
use Chamilo\CoreBundle\Repository\GradeBookCategoryRepository;
use Chamilo\CoreBundle\Repository\Node\AccessUrlRepository;
use Chamilo\CoreBundle\Repository\Node\CourseRepository;
use Chamilo\CoreBundle\Repository\Node\IllustrationRepository;
use Chamilo\CoreBundle\Repository\Node\MessageAttachmentRepository;
use Chamilo\CoreBundle\Repository\Node\UsergroupRepository;
use Chamilo\CoreBundle\Repository\Node\UserRepository;
use Chamilo\CoreBundle\Repository\SequenceRepository;
use Chamilo\CoreBundle\Repository\SequenceResourceRepository;
use Chamilo\CoreBundle\Repository\SessionRepository;
use Chamilo\CoreBundle\Settings\SettingsManager;
use Chamilo\CoreBundle\ToolChain;
use Chamilo\CourseBundle\Repository\CAnnouncementAttachmentRepository;
use Chamilo\CourseBundle\Repository\CAnnouncementRepository;
use Chamilo\CourseBundle\Repository\CAttendanceRepository;
use Chamilo\CourseBundle\Repository\CBlogRepository;
use Chamilo\CourseBundle\Repository\CCalendarEventAttachmentRepository;
use Chamilo\CourseBundle\Repository\CCalendarEventRepository;
use Chamilo\CourseBundle\Repository\CCourseDescriptionRepository;
use Chamilo\CourseBundle\Repository\CDocumentRepository;
use Chamilo\CourseBundle\Repository\CExerciseCategoryRepository;
use Chamilo\CourseBundle\Repository\CForumAttachmentRepository;
use Chamilo\CourseBundle\Repository\CForumCategoryRepository;
use Chamilo\CourseBundle\Repository\CForumForumRepository;
use Chamilo\CourseBundle\Repository\CForumPostRepository;
use Chamilo\CourseBundle\Repository\CForumThreadRepository;
use Chamilo\CourseBundle\Repository\CGlossaryRepository;
use Chamilo\CourseBundle\Repository\CGroupCategoryRepository;
use Chamilo\CourseBundle\Repository\CGroupRepository;
use Chamilo\CourseBundle\Repository\CLinkCategoryRepository;
use Chamilo\CourseBundle\Repository\CLinkRepository;
use Chamilo\CourseBundle\Repository\CLpCategoryRepository;
use Chamilo\CourseBundle\Repository\CLpItemRepository;
use Chamilo\CourseBundle\Repository\CLpRepository;
use Chamilo\CourseBundle\Repository\CNotebookRepository;
use Chamilo\CourseBundle\Repository\CQuizQuestionCategoryRepository;
use Chamilo\CourseBundle\Repository\CQuizQuestionRepository;
use Chamilo\CourseBundle\Repository\CQuizRepository;
use Chamilo\CourseBundle\Repository\CShortcutRepository;
use Chamilo\CourseBundle\Repository\CStudentPublicationAssignmentRepository;
use Chamilo\CourseBundle\Repository\CStudentPublicationCommentRepository;
use Chamilo\CourseBundle\Repository\CStudentPublicationCorrectionRepository;
use Chamilo\CourseBundle\Repository\CStudentPublicationRepository;
use Chamilo\CourseBundle\Repository\CSurveyQuestionRepository;
use Chamilo\CourseBundle\Repository\CSurveyRepository;
use Chamilo\CourseBundle\Repository\CThematicAdvanceRepository;
use Chamilo\CourseBundle\Repository\CThematicPlanRepository;
use Chamilo\CourseBundle\Repository\CThematicRepository;
use Chamilo\CourseBundle\Repository\CWikiRepository;
use Chamilo\CourseBundle\Settings\SettingsCourseManager;
use CourseManager;
use Database;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class Container
 * This class is a way to access Symfony2 services in legacy Chamilo code.
 */
class Container
{
    public static ?ContainerInterface $container = null;
    public static ?SessionInterface $session = null;
    public static ?Request $request = null;
    public static ?TranslatorInterface $translator = null;
    public static Environment $twig;
    public static string $legacyTemplate = '@ChamiloCore/Layout/layout_one_col.html.twig';

    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }

    public static function getParameter(string $parameter)
    {
        if (self::$container->hasParameter($parameter)) {
            return self::$container->getParameter($parameter);
        }

        return false;
    }

    /**
     * @return string
     */
    public static function getEnvironment()
    {
        return self::$container->get('kernel')->getEnvironment();
    }

    public static function getLogDir(): string
    {
        return self::$container->get('kernel')->getLogDir();
    }

    public static function getCacheDir(): string
    {
        return self::$container->get('kernel')->getCacheDir().'/';
    }

    /**
     * @return string
     */
    public static function getProjectDir()
    {
        if (null !== self::$container) {
            return self::$container->get('kernel')->getProjectDir().'/';
        }

        return str_replace('\\', '/', realpath(__DIR__.'/../../../')).'/';
    }

    /**
     * @return bool
     */
    public static function isInstalled()
    {
        return self::$container->get('kernel')->isInstalled();
    }

    /**
     * @return Environment
     */
    public static function getTwig()
    {
        return self::$twig;
    }

    /**
     * @return Editor
     */
    public static function getHtmlEditor()
    {
        return self::$container->get('chamilo_core.html_editor');
    }

    /**
     * @return null|Request
     */
    public static function getRequest()
    {
        if (null === self::$container) {
            return null;
        }

        if (!empty(self::$request)) {
            return self::$request;
        }

        return self::$container->get('request_stack')->getCurrentRequest();
    }

    public static function setRequest(Request $request): void
    {
        self::$request = $request;
    }

    /**
     * @return false|Session
     */
    public static function getSession()
    {
        if (null !== self::$container) {
            return self::$container->get('session');
        }

        return false;
    }

    /**
     * @return AuthorizationChecker
     */
    public static function getAuthorizationChecker()
    {
        return self::$container->get('security.authorization_checker');
    }

    /**
     * @return TokenStorage|TokenStorageInterface
     */
    public static function getTokenStorage()
    {
        return self::$container->get('security.token_storage');
    }

    /**
     * @return TranslatorInterface
     */
    public static function getTranslator()
    {
        if (null !== self::$translator) {
            return self::$translator;
        }

        //if (self::$container->has('translator')) {
        return self::$container->get('translator');
        //}
    }

    public static function getMailer(): Mailer
    {
        return self::$container->get(Mailer::class);
    }

    public static function getSettingsManager(): SettingsManager
    {
        return self::$container->get('chamilo.settings.manager');
    }

    public static function getCourseSettingsManager(): SettingsCourseManager
    {
        return self::$container->get(SettingsCourseManager::class);
    }

    /**
     * @return EntityManager
     */
    public static function getEntityManager()
    {
        return Database::getManager();
    }

    public static function getAttendanceRepository(): CAttendanceRepository
    {
        return self::$container->get(CAttendanceRepository::class);
    }

    public static function getAnnouncementRepository(): CAnnouncementRepository
    {
        return self::$container->get(CAnnouncementRepository::class);
    }

    public static function getAccessUrlRepository(): AccessUrlRepository
    {
        return self::$container->get(AccessUrlRepository::class);
    }

    public static function getAnnouncementAttachmentRepository(): CAnnouncementAttachmentRepository
    {
        return self::$container->get(CAnnouncementAttachmentRepository::class);
    }

    public static function getCourseRepository(): CourseRepository
    {
        return self::$container->get(CourseRepository::class);
    }

    public static function getSessionRepository(): SessionRepository
    {
        return self::$container->get(SessionRepository::class);
    }

    public static function getCourseCategoryRepository(): CourseCategoryRepository
    {
        return self::$container->get(CourseCategoryRepository::class);
    }

    public static function getCourseDescriptionRepository(): CCourseDescriptionRepository
    {
        return self::$container->get(CCourseDescriptionRepository::class);
    }

    public static function getGlossaryRepository(): CGlossaryRepository
    {
        return self::$container->get(CGlossaryRepository::class);
    }

    public static function getCalendarEventRepository(): CCalendarEventRepository
    {
        return self::$container->get(CCalendarEventRepository::class);
    }

    public static function getCalendarEventAttachmentRepository(): CCalendarEventAttachmentRepository
    {
        return self::$container->get(CCalendarEventAttachmentRepository::class);
    }

    public static function getDocumentRepository(): CDocumentRepository
    {
        return self::$container->get(CDocumentRepository::class);
    }

    public static function getQuizRepository(): CQuizRepository
    {
        return self::$container->get(CQuizRepository::class);
    }

    public static function getExerciseCategoryRepository(): CExerciseCategoryRepository
    {
        return self::$container->get(CExerciseCategoryRepository::class);
    }

    public static function getForumRepository(): CForumForumRepository
    {
        return self::$container->get(CForumForumRepository::class);
    }

    public static function getForumCategoryRepository(): CForumCategoryRepository
    {
        return self::$container->get(CForumCategoryRepository::class);
    }

    public static function getForumPostRepository(): CForumPostRepository
    {
        return self::$container->get(CForumPostRepository::class);
    }

    public static function getForumAttachmentRepository(): CForumAttachmentRepository
    {
        return self::$container->get(CForumAttachmentRepository::class);
    }

    public static function getForumThreadRepository(): CForumThreadRepository
    {
        return self::$container->get(CForumThreadRepository::class);
    }

    public static function getGradeBookCategoryRepository(): GradeBookCategoryRepository
    {
        return self::$container->get(GradeBookCategoryRepository::class);
    }

    public static function getGroupRepository(): CGroupRepository
    {
        return self::$container->get(CGroupRepository::class);
    }

    public static function getGroupCategoryRepository(): CGroupCategoryRepository
    {
        return self::$container->get(CGroupCategoryRepository::class);
    }

    public static function getQuestionRepository(): CQuizQuestionRepository
    {
        return self::$container->get(CQuizQuestionRepository::class);
    }

    public static function getQuestionCategoryRepository(): CQuizQuestionCategoryRepository
    {
        return self::$container->get(CQuizQuestionCategoryRepository::class);
    }

    public static function getLinkRepository(): CLinkRepository
    {
        return self::$container->get(CLinkRepository::class);
    }

    public static function getLinkCategoryRepository(): CLinkCategoryRepository
    {
        return self::$container->get(CLinkCategoryRepository::class);
    }

    public static function getLpRepository(): CLpRepository
    {
        return self::$container->get(CLpRepository::class);
    }

    public static function getLpItemRepository(): CLpItemRepository
    {
        return self::$container->get(CLpItemRepository::class);
    }

    public static function getLpCategoryRepository(): CLpCategoryRepository
    {
        return self::$container->get(CLpCategoryRepository::class);
    }

    public static function getMessageAttachmentRepository(): MessageAttachmentRepository
    {
        return self::$container->get(MessageAttachmentRepository::class);
    }

    public static function getNotebookRepository(): CNotebookRepository
    {
        return self::$container->get(CNotebookRepository::class);
    }

    public static function getUserRepository(): UserRepository
    {
        return self::$container->get(UserRepository::class);
    }

    public static function getUsergroupRepository(): UsergroupRepository
    {
        return self::$container->get(UsergroupRepository::class);
    }

    public static function getIllustrationRepository(): IllustrationRepository
    {
        return self::$container->get(IllustrationRepository::class);
    }

    public static function getShortcutRepository(): CShortcutRepository
    {
        return self::$container->get(CShortcutRepository::class);
    }

    public static function getStudentPublicationRepository(): CStudentPublicationRepository
    {
        return self::$container->get(CStudentPublicationRepository::class);
    }

    public static function getStudentPublicationAssignmentRepository(): CStudentPublicationAssignmentRepository
    {
        return self::$container->get(CStudentPublicationAssignmentRepository::class);
    }

    public static function getStudentPublicationCommentRepository(): CStudentPublicationCommentRepository
    {
        return self::$container->get(CStudentPublicationCommentRepository::class);
    }

    public static function getStudentPublicationCorrectionRepository(): CStudentPublicationCorrectionRepository
    {
        return self::$container->get(CStudentPublicationCorrectionRepository::class);
    }

    public static function getSequenceResourceRepository(): SequenceResourceRepository
    {
        return self::$container->get(SequenceResourceRepository::class);
    }

    public static function getSequenceRepository(): SequenceRepository
    {
        return self::$container->get(SequenceRepository::class);
    }

    public static function getSurveyRepository(): CSurveyRepository
    {
        return self::$container->get(CSurveyRepository::class);
    }

    public static function getSurveyQuestionRepository(): CSurveyQuestionRepository
    {
        return self::$container->get(CSurveyQuestionRepository::class);
    }

    public static function getThematicRepository(): CThematicRepository
    {
        return self::$container->get(CThematicRepository::class);
    }

    public static function getThematicPlanRepository(): CThematicPlanRepository
    {
        return self::$container->get(CThematicPlanRepository::class);
    }

    public static function getThematicAdvanceRepository(): CThematicAdvanceRepository
    {
        return self::$container->get(CThematicAdvanceRepository::class);
    }

    public static function getBlogRepository(): CBlogRepository
    {
        return self::$container->get(CBlogRepository::class);
    }

    public static function getWikiRepository(): CWikiRepository
    {
        return self::$container->get(CWikiRepository::class);
    }

    public static function getFormFactory(): FormFactory
    {
        return self::$container->get('form.factory');
    }

    /**
     * @param string $type error|success|warning|danger
     */
    public static function addFlash(string $message, string $type = 'success'): void
    {
        $session = self::getSession();
        $session->getFlashBag()->add($type, $message);
    }

    public static function getRouter(): Router
    {
        return self::$container->get('router');
    }

    public static function getToolChain(): ToolChain
    {
        return self::$container->get(ToolChain::class);
    }

    public static function getAssetRepository(): AssetRepository
    {
        return self::$container->get(AssetRepository::class);
    }

    public static function setLegacyServices(ContainerInterface $container, bool $setSession = true): void
    {
        Database::setConnection($container->get('doctrine.dbal.default_connection'));
        $em = $container->get('doctrine.orm.entity_manager');
        Database::setManager($em);
        // Setting course tool chain (in order to create tools to a course)
        //CourseManager::setToolList($container->get(ToolChain::class));
        /*if ($setSession) {
            self::$session = $container->get('session');
        }*/
    }
}
