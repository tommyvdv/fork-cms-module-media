<?php

namespace Backend\Modules\Media\Actions;

use Backend\Core\Engine\Base\ActionIndex;
use Backend\Core\Engine\Authentication;
use Backend\Core\Engine\Language;
use Backend\Core\Engine\Model;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;

/**
 * This is the index-action (default), it will display the overview of Media posts
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class Index extends ActionIndex
{
    /**
     * Execute the action
     */
    public function execute()
    {
        parent::execute();

        // add js
        $this->header->addJS('jstree/jstree.min.js', null, false);
        $this->header->addJS('MediaInit.js', null, true);

        // add css
        $this->header->addCSS('/src/Backend/Modules/Media/Js/jstree/themes/default/style.css', null, true);

        $this->getData();

        $this->parse();
        $this->display();
    }
    /**
     * Get the data
     */
    protected function getData()
    {
        $this->folder_id = $this->getParameter('folder_id', 'int');
        $this->tree = BackendMediaModel::getFolderTreeHTML();
        $this->folder = BackendMediaModel::getFolder($this->folder_id);
        $this->library = BackendMediaModel::getLibraryForFolder($this->folder_id);
    }


    /**
     * Parse the page
     */
    protected function parse()
    {   
        $this->tpl->assign("library", $this->library);
        $this->tpl->assign("folder", $this->folder);
        $this->tpl->assign("tree", $this->tree);
        $this->header->addJSData('media','folder_id', $this->folder_id);
    }
}
