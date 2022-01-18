<?php

namespace Drupal\versiontracker\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Messenger;
use Drupal\Core\Link;

class SubmitForm extends FormBase {
    public function getFormid() {
        return 'submit_form';
    }
    public function buildform(array $form, FormStateInterface $form_state) {

        $form['action']=['#type'=>'action',];

        $form['action']['submit']=['#type'=>'submit','#value'=>t('Submit'),];

        return $form;

    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        
        $conn = Database::getConnection();

        $query = $conn->select('test_sites','m');
        $query->fields('m',['id','url','active']);
        $result = $query->execute()->fetchAll();

        $date = date('Y-m-d H:i:s');

        foreach($result as $value) {
            if($value->active) {
                $url = $value->url;
                $tags = get_meta_tags($url);
                if($tags and $tags['generator']) {
                    $version = $tags['generator'];
                } else {
                    $version = 'unknown';
                }
                $field = ['test_date'=>$date,'url'=>$value->url,'wp_version'=>$version,];
                $query = \Drupal::database();
                $query->insert('test_results')->fields($field)->execute();
            }
        }

        $this->messenger()->addMessage('Sucessfully Submitted Records');
        $form_state->setRedirect('versiontracker.versiontracker_controller_listtests');

    }
}




?>