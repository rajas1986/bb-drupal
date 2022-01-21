<?php
/**
 * @file
 * Contains \Drupal\bb_custom\Controller\MapiController.
 */

namespace Drupal\bb_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\ClientInterface;

use Drupal\Component\Render\FormattableMarkup;

/**
 * Controller routines for mapi routes.
 */
class MapiController extends ControllerBase {

  protected $httpClient;
  public $apiMode;
  public $apiEndpoint;
  public $apiUsername;
  public $apiPassword;
  public $head;

  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }

   /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client')
    );
  }

  public function nodeviewcount( Request $request ) {

    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $request->request->replace( is_array( $data ) ? $data : [] );
      if(empty($data["nodeid"]) || empty($data["ipaddress"])){
        $response['head']["statusMessage"] = array("message"=>"Input values are missing");
        $response['head']["statusCode"] = "403";
      } else {
        $database = \Drupal::database();
        $query = $database->select('custom_node_views', 'cnv');   
        $query->condition('cnv.nid', $data["nodeid"]);
        $query->condition('cnv.ip_address', $data['ipaddress']);
        $query->condition('cnv.date', date("Y-m-d"));
        $query->fields('cnv', ['view_id', 'view_count', 'ip_address', 'date', 'nid']);
        $results = $query->execute()->fetchAll();
        if(empty($results)){
            $insquery = $database->insert('custom_node_views')->fields(['view_count', 'ip_address', 'date', 'nid']);
            $insquery->values([
                'view_count'=>1, 
                'ip_address'=>$data["ipaddress"], 
                'date' => date('Y-m-d'),
                'nid' => $data["nodeid"]
            ]);
            $insquery->execute();
            $response['head']["statusMessage"] = array("message"=>"View count added");
            $response['head']["statusCode"] = "200";
        } else {
            $response['head']["statusMessage"] = array("message"=>"View count already added");
            $response['head']["statusCode"] = "403";
        }
        $selquery = $database->select('custom_node_views', 'cnv');   
        $selquery->condition('cnv.nid', $data["nodeid"]);
        $selquery->condition('cnv.ip_address', $data['ipaddress']);
        $selquery->fields('cnv', ['view_id', 'view_count', 'ip_address', 'date', 'nid']);
        $selresults = $selquery->execute()->fetchAll();
        $viewcount = 0;
        foreach ($selresults as $record) {
            $viewcount = $viewcount + $record->view_count;
        }
        $response['head']["view_count"] = $viewcount;
      }
    } else {
      $response['head']["statusMessage"] = array("message"=>"Error in headers");
      $response['head']["statusCode"] = "403";
    }
    return new JsonResponse($response);
  }

  public function totalvotecount( Request $request ) {

    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $request->request->replace( is_array( $data ) ? $data : [] );
      if(empty($data["nodeid"]) || empty($data["ipaddress"])){
        $response['head']["statusMessage"] = array("message"=>"Input values are missing");
        $response['head']["statusCode"] = "403";
      } else {
        $database = \Drupal::database();
        $query = $database->select('custom_vote_mapping', 'cvm');   
        $query->condition('cvm.nid', $data["nodeid"]);
        $query->fields('cvm', ['id', 'nid', 'votes']);
        $cvmresults = $query->execute()->fetchAssoc();
        $totalvotes = 0;

        $query = $database->select('custom_node_voting', 'cnv');   
        $query->condition('cnv.nid', $data["nodeid"]);
        $query->condition('cnv.ip_address', $data["ipaddress"]);
        $query->fields('cnv', ['voting_id', 'vote_count', 'nid','ip_address','date']);
        $cnvresults = $query->execute()->fetchAssoc();

        if(empty($cnvresults)){
            $response['head']["votecount"] = 0;
        } else {
            $response['head']["votecount"] = $cnvresults['vote_count'];
        }

        if(!empty($cvmresults)){
            $totalvotes = $cvmresults['votes'];
        }
        $response['head']["statusMessage"] = array("message"=>"Votes");
            $response['head']["statusCode"] = "200";
        $response['head']["totalvotes"] = $totalvotes;
      }
    } else {
      $response['head']["statusMessage"] = array("message"=>"Error in headers");
      $response['head']["statusCode"] = "403";
    }
    return new JsonResponse($response);
  }

  public function votingupdown( Request $request ) {

    if ( 0 === strpos( $request->headers->get( 'Content-Type' ), 'application/json' ) ) {
      $data = json_decode( $request->getContent(), TRUE );
      $request->request->replace( is_array( $data ) ? $data : [] );
      if(empty($data["nodeid"]) || empty($data["ipaddress"]) || empty($data["vote"])){
        $response['head']["statusMessage"] = array("message"=>"Input values are missing");
        $response['head']["statusCode"] = "403";
      } else {
        $database = \Drupal::database();
        $query = $database->select('custom_node_voting', 'cnv');   
        $query->condition('cnv.nid', $data["nodeid"]);
        $query->condition('cnv.ip_address', $data["ipaddress"]);
        $query->fields('cnv', ['voting_id', 'vote_count', 'nid','ip_address','date']);
        $results = $query->execute()->fetchAssoc();

        $sql = "SELECT SUM(votes) as total_votes,nid  FROM custom_vote_mapping WHERE nid = :id";
        $query = $database->query($sql, [':id' => $data["nodeid"]]);
        $record = $query->fetchAssoc();
        if($data["vote"]=="up"){
            $vote_count = 1;
            $total_count = $record['total_votes'] + 1;
            if($total_count==0){
                $total_count=1;
            }
        } else if($data["vote"]=="down"){
            $vote_count = -1;
            $total_count = $record['total_votes'] - 1;
            if($total_count==0){
                $total_count=-1;
            }
        }

        if(empty($results)){
            $insCNV = $database->insert('custom_node_voting')
                      ->fields(['vote_count', 'nid', 'ip_address', 'date'])
                      ->values([
                        'vote_count' => $vote_count,
                        'nid' => $data['nodeid'],
                        'ip_address' => $data['ipaddress'],
                        'date' => date('Y-m-d')
                        //'created' => \Drupal::time()->getRequestTime(),
                      ])
                      ->execute();
        } else {
            $num_updated = $database->update('custom_node_voting')
                      ->fields([
                        'vote_count' => $vote_count 
                      ])
                      ->condition('nid', $data["nodeid"])
                      ->condition('ip_address', $data["ipaddress"])
                      ->execute();
        }

        if(empty($record['total_votes'])){
            $insCVM = $database->insert('custom_vote_mapping')
                      ->fields(['nid', 'votes'])
                      ->values([
                        'nid' => $data['nodeid'],
                        'votes' => $total_count
                        //'created' => \Drupal::time()->getRequestTime(),
                      ])
                      ->execute();   
        } else {
            $tot_count_updated = $database->update('custom_vote_mapping')
                  ->fields([
                    'votes' => $total_count 
                  ])
                  ->condition('nid', $data["nodeid"])
                  ->execute();
        }
        $response['head']["statusMessage"] = array("message"=>"Votes updated!");
        $response['head']["statusCode"] = "200";
        $response['head']["votecount"] = $vote_count;
        $response['head']["totalcount"] = $total_count;
      }
    } else {
      $response['head']["statusMessage"] = array("message"=>"Error in headers");
      $response['head']["statusCode"] = "403";
    }
    return new JsonResponse($response);
  }

  public function searchautocomplete( Request $request ){
    $phrase = \Drupal::request()->request->get('phrase');
    
    if (!empty($phrase)) {
      $newwords = $phrase.'';
      /*$query = \Drupal::entityQuery('node')
        ->addTag('debug')
        ->condition('type', 'support_pages')
        ->condition('title', '%' . $newwords . '%', 'LIKE')
        ->sort('nid', 'ASC');
      $nids = $query->execute();*/
      $database = \Drupal::database();
      $sql = 'SELECT `title`  FROM `node_field_data` WHERE `type` = "support_pages" AND `title` LIKE "%'.$newwords.'%"';
      $sql = "SELECT * FROM `node_field_data` WHERE `type` LIKE 'support_pages' AND `title` LIKE '%".$newwords."%'";
      //echo $sql; 
      $query = $database->query($sql);
      //echo "<pre/>"; print_r($query); exit; 
      $nodeArr = array();
      $result = $query->fetchAll();
      //echo "<pre>jhlll"; print_r($result); exit;
      foreach($result as $row){
        array_push($nodeArr,['name'=>$row->title,'path'=>\Drupal::service('path_alias.manager')->getAliasByPath('/node/'.$row->nid)]);
      }
    } else {
      $response['head']["statusMessage"] = array("message"=>"Error in headers");
      $response['head']["statusCode"] = "403";
    }
    return new JsonResponse($nodeArr);
  }
}