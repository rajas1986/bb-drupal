<?php

    namespace Drupal\bb_custom\Plugin\Block;

    use Drupal\Core\Block\BlockBase;
    use Drupal\Core\Form\FormInterface;
    use Drupal\Core\Form\FormStateInterface;
    use Drupal\file\Entity\File;

   /**
     * Provides a 'Voting Up Down' block.
     *
     * @Block(
     *   id = "voting_up_down_custom_block",
     *   admin_label = @Translation("Voting Up Down Block"),
     *   category = @Translation("BB App Module Blocks")
     * )
    */
    class VotingUpDownBlock extends BlockBase {
      
     /**
      * {@inheritdoc}
     */
     public function build() {
      $node = \Drupal::routeMatch()->getParameter('node');
      if ($node instanceof \Drupal\node\NodeInterface) {
        $currentnid = $node->id();
        $database = \Drupal::database();
        $sql = "SELECT SUM(votes) as total_votes,nid  FROM custom_vote_mapping WHERE nid = :id";
        $query = $database->query($sql, [':id' => $currentnid]);
        if ($query) {
          $row = $query->fetchAssoc();
          //echo "<pre>"; print_r($row); exit;
        }
      }
      return [
          '#theme' => 'voting_up_down',
          '#data' => ["nodeid"=>$currentnid,"row"=>$row],
          '#attached' => [
            'library' => [
              'bb_custom/voting_assets_attachments',
            ],
          ],
      ];
     }
    
    public function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
  }