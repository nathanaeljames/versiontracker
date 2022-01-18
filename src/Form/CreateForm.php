<?php

namespace Drupal\versiontracker\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Messenger;
use Drupal\Core\Link;

class CreateForm extends FormBase {
    public function getFormid() {
        return 'create_form';
    }
    public function buildform(array $form, FormStateInterface $form_state) {
        $conn = Database::getConnection();
        $record = [];
        if(isset($_GET['id'])) {
            $query = $conn->select('test_sites','m')->condition('id',$_GET['id'])->fields('m');
            $record = $query->execute()->fetchAssoc();
        }

        $form['url'] = ['#type'=>'url','#title'=>t('Url'),'#required'=>TRUE,'#default_value'=>(
            isset($record['url'])&&$_GET['id'])? $record['url']:'',];

        $form['notes'] = ['#type'=>'textfield','#title'=>t('Notes'),'#required'=>FALSE,'#default_value'=>(
            isset($record['notes'])&&$_GET['id'])? $record['notes']:'',];

        $form['active'] = ['#type'=>'checkbox','#title'=>t('Active'),'#required'=>FALSE,'#default_value'=>(
            isset($record['active'])&&$_GET['id'])? $record['active']:'',];

        $form['action']=['#type'=>'action',];

        $form['action']['submit']=['#type'=>'submit','#value'=>t('Save'),];

        $form['action']['reset']=['#type'=>'button','#value'=>t('Reset'),'#attributes'=>['onclick'=>
        'this.form.reset(); return false;',],];

        $link = Url::fromUserInput('/version_tracker');

        $form['action']['cancel']=['#markup'=>Link::fromTextAndUrl(t('Back to page'),$link,['attributes'=>
        ['class'=>'button']])->toString(),];

        return $form;

    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
        # validation for browsers that don't auto-validate url input type
        $url = $form_state->getValue('url');
        if(!preg_match('/https?:\/\/.*/i',$url)) {
            $form_state->setErrorByName('url',$this->t('Please enter valid URL with http or https prefix'));
        }
    
        parent::validateForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        $field = $form_state->getValues();
        $url = $field['url'];
        $notes = $field['notes'];
        $active = $field['active'];
        $date = date('Y-m-d H:i:s');

        if(isset($_GET['id'])) {
            $field = ['updated_at'=>$date,'url'=>$url,'notes'=>$notes,'active'=>$active,];
            $query = \Drupal::database();
            $query->update('test_sites')->fields($field)->condition('id',$_GET['id'])->execute();
            $this->messenger()->addMessage('Sucessfully Updated Records');
        } else {
            $field = ['created_at'=>$date,'url'=>$url,'notes'=>$notes,'active'=>$active,];
            $query = \Drupal::database();
            $query->insert('test_sites')->fields($field)->execute();
            $this->messenger()->addMessage('Sucessfully Saved Records');
            $form_state->setRedirect('versiontracker.versiontracker_controller_listsites');
        }
    }
}




?>