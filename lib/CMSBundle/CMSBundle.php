<?php
namespace EgioDigital\CMSBundle;

use EgioDigital\CMSBundle\DependencyInjection\CMSBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CMSBundle extends Bundle
{
   public function getContainerExtension()
   {
      return new CMSBundleExtension();
   }

}