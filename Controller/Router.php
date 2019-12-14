<?php

namespace Tbb\Blog\Controller;

use Magento\Framework\DataObject;

class Router implements \Magento\Framework\App\RouterInterface {

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_response = $response;
    }

    /**
     * Validate and Match
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request) {

        /*
         * You can use any name in URL and add condition for that name here to redirect it. Here we are accessing customrouting
         * and if the we get the same, the we will redirect the request to controller action.
         * We will search “examplerouter” and “exampletocms” words and make forward depend on word
         * -examplerouter will forward to base router to match inchootest front name, test controller path and test controller class
         * -exampletocms will set front name to cms, controller path to page and action to view
         */
        // $a = explode('/', $_SERVER['REQUEST_URI']);
        // $postId = end($a);  [if you want to pass parameter also.]
        //exit;
        $identifier = trim($request->getPathInfo(), '/');
        if (strpos($identifier, 'exampletocms') !== false) {
            /*
             * We must set module, controller path and action name + we will set page id 5 witch is about us page on
             * default magento 2 installation with sample data.
             */
            $request->setModuleName('cms')->setControllerName('page')->setActionName('view')->setParam('page_id', 4); // specify the page id
        } else if (strpos($identifier, 'blog') !== false) {
            /*
             * We must set module, controller path and action name for our controller class(Controller/Test/Test.php)
             */
            $data = new DataObject([
                'module_name'     => 'blog',
                'controller_name' => 'post',
                'action_name'     => 'listing',
                'params'          => [1],
            ]);
            $request
                ->setModuleName($data->getModuleName())
                ->setControllerName($data->getControllerName())
                ->setActionName($data->getActionName())
                ->setParams($data->getParams());

            // $request->setParam('id', $postId); [if passing params]
        } else {
            //There is no match
            return;
        }

        /*
         * We have match and now we will forward action
         */
        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward', ['request' => $request]
        );
    }

}


/* you will face iteration error if the controller you are trying to access is not available*/