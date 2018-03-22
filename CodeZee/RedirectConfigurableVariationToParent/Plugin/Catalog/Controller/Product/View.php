<?php
namespace CodeZee\RedirectConfigurableVariationToParent\Plugin\Catalog\Controller\Product;

class View
{
    /**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $http;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurable;

    protected $_productloader;

    /**
     * @param \Magento\Framework\App\Response\Http $http
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
     * @param \Magento\Catalog\Model\ProductFactory $_productloader
     */
    public function __construct(
        \Magento\Framework\App\Response\Http $http,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Magento\Catalog\Model\ProductFactory $_productloader
    ) {
        $this->http = $http;
        $this->productHelper =$productHelper;
        $this->configurable = $configurable;
        $this->_productloader = $_productloader;
    }

    /**
     * Product view action
     *
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\Result\Redirect
     */
    public function aroundExecute(
        \Magento\Catalog\Controller\Product\View $subject,
        \Closure $proceed
    )
    {
        $productId = (int) $subject->getRequest()->getParam('id');
        $parentIds = $this->configurable->getParentIdsByChild($productId);
        $parentId = array_shift($parentIds);

        if($parentId) {
            $categoryId = (int)$subject->getRequest()->getParam('category', false);
            $productId = (int)$parentId;

            $params = new \Magento\Framework\DataObject();
            $params->setCategoryId($categoryId);

            $parentProduct = $this->_productloader->create()->load($productId);

            header('Location: '.$parentProduct->getProductUrl());
            exit();

        }
        return $proceed();
    }
}
