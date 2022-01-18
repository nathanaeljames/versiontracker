<?php

namespace Drupal\versiontracker\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Messenger;

class VersionTrackerController extends ControllerBase {
    public function listsites() {
        //Table Header
        $header_table = ['id'=>t('ID'),'url'=>t('Url'),'active'=>t('Active'),'notes'=>t('Notes'),
        'opt'=>t('Operation'),'opt1'=>t('Operation'),'created'=>t('Created'),'updated'=>t('Updated')];
        $row = [];

        $conn = Database::getConnection();

        $query = $conn->select('test_sites','m');
        $query->fields('m',['id','created_at','updated_at','url','active','notes']);
        $result = $query->execute()->fetchAll();

        foreach($result as $value) {
            $delete = Url::fromUserInput('/version_tracker/destroy/'.$value->id);
            $edit = Url::fromUserInput('/version_tracker/create?id='.$value->id);

            $row[] = ['id'=>$value->id,'url'=>$value->url,'active'=>$value->active,'notes'=>$value->notes,
            'opt'=>Link::fromTextAndUrl('Edit',$edit)->toString(),'opt1'=>Link::fromTextAndUrl('Delete',
            $delete)->toString(),'created'=>$value->created_at,'updated'=>$value->updated_at,];
        }

        $add = Url::fromUserInput('/version_tracker/create');

        $text = "Add Site to Check";

        $footer[] = [
            [
                'data' => [
                '#form' => 'Form',
                \Drupal::formBuilder()->getForm('Drupal\versiontracker\Form\SubmitForm')
                ],
                'colspan' => 8
            ]
        ];

        $data['table'] = ['#type'=>'table','#header'=>$header_table,'#rows'=>$row,'#empty'=>t('No record found'),
        '#caption'=>Link::fromTextAndUrl($text,$add)->toString(),'#footer'=>$footer];

        $this->messenger()->addMessage('Records Listed');

        return $data;
    }

    public function listtests() {
        //Table Header
        $header_table = ['id'=>t('ID'),'url'=>t('Url'),'test_date'=>t('Test Date'),'wp_version'=>t('WP Version'),];
        $row = [];

        $conn = Database::getConnection();

        $query = $conn->select('test_results','m');
        $query->fields('m',['id','url','test_date','wp_version']);
        $result = $query->execute()->fetchAll();

        foreach($result as $value) {
            $row[] = ['id'=>$value->id,'url'=>$value->url,'test_date'=>$value->test_date,'wp_version'=>$value->wp_version,];
        }

        $add = Url::fromUserInput('/version_tracker');

        $text = "Test More Sites";

        $data['table'] = ['#type'=>'table','#header'=>$header_table,'#rows'=>$row,'#empty'=>t('No record found'),
        '#caption'=>Link::fromTextAndUrl($text,$add)->toString(),];

        $this->messenger()->addMessage('Records Listed');

        return $data;
    }
}

?>