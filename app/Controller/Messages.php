<?php


class Controller_Messages 
{


    public function getMessages()
    {
    
        $where = "1";
        $bind = array();

        $first = true;

        if ( isset( $_REQUEST['tags'] ) ) {
            $tags = explode(',', $tags );
            foreach ( $tags as $tag ) {
                $where .= ( $first ? '' : ' or ' ) . "tags like '%".$tag."%' "; 
            }
        }


        $messageModel = new Model_Message();
        $collection = $messageModel->getCollection()->load($where,$bind);

        echo json_encode( $collection->getDataAsArray() );

    }




}
