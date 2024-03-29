<?php

namespace Backend\Modules\Media\Actions;

use Backend\Core\Engine\Base\ActionDelete;
use Backend\Core\Engine\Model;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Finder\Finder;

use Frontend\Modules\Media\Engine\Helper as FrontendMediaHelper;

/**
 * This is the delete-action, it deletes an item
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 */
class DeleteFile extends ActionDelete
{
    /**
     * Execute the action
     */
    public function execute()
    {
        $this->id = $this->getParameter('id', 'int');

        // does the item exist
        if ($this->id !== null && BackendMediaModel::existsFile($this->id)) {
            parent::execute();
            $this->record = (array) BackendMediaModel::getFile($this->id);

            BackendMediaModel::deleteFile($this->id);

            $files_path = FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_FILES_FOLDER;
            $preview_files_path = FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_PREVIEW_FILES_FOLDER;
            $generated_files_path = FRONTEND_FILES_PATH . '/' . FrontendMediaHelper::SETTING_GENERATED_FILES_FOLDER;

            $fs = new Filesystem();
            $fs->remove($files_path . '/' . $this->record['filename']);
            $fs->remove($files_path . '/' . $this->record['original_filename']);

            $fs->remove($preview_files_path . '/' . $this->record['filename']);


            $finder = new Finder();
            $fs = new Filesystem();
            $fs->mkdir($generated_files_path, 0775);
            foreach ($finder->directories()->in($generated_files_path) as $directory) {
                $fileName = $directory->getRealPath() . '/' . $this->record['filename'];
                if (is_file($fileName)) {
                    $fs->remove($fileName);
                }
            }

            $this->redirect(
                Model::createURLForAction('Index') . '&report=deleted&folder_id=' . $this->record['folder_id'] 
            );
        }
        else $this->redirect(Model::createURLForAction('Index') . '&error=non-existing');
    }
}
