<?php

    namespace Drupal\bb_custom\Plugin\Block;

    use Drupal\Core\Block\BlockBase;
    use Drupal\Core\Form\FormInterface;
    use Drupal\Core\Form\FormStateInterface;
    use Drupal\file\Entity\File;
    use Drupal\image\Entity\ImageStyle;

   /**
     * Provides a 'More from article category' block.
     *
     * @Block(
     *   id = "more_from_article_category_custom_block",
     *   admin_label = @Translation("More From Article Category Block"),
     *   category = @Translation("BB App Module Blocks")
     * )
    */
    class MoreFromArticleCategoryBlock extends BlockBase {
      
     /**
      * {@inheritdoc}
     */
     public function build() {
      $node = \Drupal::routeMatch()->getParameter('node');
      if ($node instanceof \Drupal\node\NodeInterface) {
        $currentnid = $node->id();
        $termids = $node->get('field_tags')->getValue();
        $termArr = array();
        foreach($termids as $termid){
          array_push($termArr, $termid['target_id']); 
        }
        //echo "<pre/>"; print_r($termArr); exit;
        
        $query = \Drupal::entityQuery('node')
          ->condition('type', 'article')
          ->condition('field_tags', $termArr, 'IN')
          ->condition('field_article_categories', 3)
          ->sort('nid', 'ASC')
          ->range(0, 6);
        $nids = $query->execute();
        //echo "<pre/>"; print_r($nids); exit;
        $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
        $nodeDataArr = array();
        $names = array();
        $nodesArr = array();
        foreach($nodes as $node){
          array_push($nodesArr, $currentnid);
          if($node->id()!=$currentnid){
            array_push($nodesArr, $node->id());
            $nodeDataArr[$node->id()]['title'] = $node->getTitle();
            $nodeDataArr[$node->id()]['short_description'] = $node->get("field_short_description")->getValue();
            $nodeDataArr[$node->id()]['image'] = ImageStyle::load('more_from_articles_category')->buildUrl($node->field_image->entity->getFileUri());
            foreach ($node->field_tags as $item) {
              if ($item->entity) {
                $nodeDataArr[$node->id()]['tags'][$item->entity->id()] = $item->entity->label();
              }
            }
            //file_create_url($node->field_image->entity->getFileUri())
            
          }
        }
        //$nidStr = implode("', '", $nodesArr);
        $database = \Drupal::database();
        $nodeViewsArr = array();
        //$query = $database->query("SELECT SUM(view_count) as total_views FROM {custom_node_views} WHERE nid IN (:ids[]) GROUP BY nid", [':ids[]' => $nodesArr]);
        $sql = "SELECT SUM(view_count) as total_views,nid  FROM custom_node_views WHERE nid IN (:ids[]) GROUP BY nid";
        $query = $database->query($sql, [':ids[]' => array_unique($nodesArr)]);
        if ($query) {
          while ($row = $query->fetchAssoc()) {
            $nodeViewsArr[$row['nid']] = $row['total_views'];
          }
        }

        //echo "<pre>"; print_r($result); exit;
      }
      return [
          '#theme' => 'blogs_more_from_article_category',
          '#data' => ['nodes'=>$nodeDataArr, 'nodeviews'=>$nodeViewsArr],
      ];
     }
     
   }