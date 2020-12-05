<?php

namespace Think1st\WordPress;

class Menu {

    public static function get() {

        $menuid = 10;
        $lang = (isset($_GET['l'])) ? $_GET['l'] : "en";
        switch($lang) {
            case "nl":
                $menuid = 40; //Dutch
                break;
            case "en":
                $menuid = 10; //English
                break;
            case "da":
                $menuid = 39; //Danish
                break;
            case "fr":
                $menuid = 41; //French
                break;
            case "de":
                $menuid = 42; //German
                break;
            case "he":
                $menuid = 43; //Hebrew
                break;
            case "pr":
                $menuid = 44; //Persian
                break;
            case "po":
                $menuid = 45; //Portogees
                break;
            case "es":
                $menuid = 46; //Spanish
                break;
        }

        $wp = \Think1st\WordPress\Db::connection();

        // Prepare a select statement
        $sql = "SELECT p.id, pm.meta_value as page, p.menu_order as menu
        FROM wp_68u2vb_posts AS p
        LEFT JOIN wp_68u2vb_term_relationships AS tr ON tr.object_id = p.ID
        LEFT JOIN wp_68u2vb_postmeta AS pm ON pm.post_id = p.ID
        LEFT JOIN wp_68u2vb_term_taxonomy AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
        WHERE p.post_type = 'nav_menu_item' 
        AND p.post_status = 'publish'
        AND pm.meta_key = '_menu_item_object_id'
        AND tt.term_id = $menuid
        ORDER BY p.menu_order ASC";
        
        /**
         * First get from the menu the true posts.
         */
        if($stmt = mysqli_prepare($wp, $sql)){
            if(mysqli_stmt_execute($stmt)){

                mysqli_stmt_bind_result($stmt, $id, $page, $menu);

                $posts = [];
                while (mysqli_stmt_fetch($stmt)) {
                    $posts[] = ["id" => $id,"page" => $page, "menu" => $menu];
                }

                /**
                 * Create SQL to find menu items.
                 */
                $sqlmenu = "SELECT id, post_title, post_name, menu_order FROM wp_68u2vb_posts WHERE ";

                foreach ($posts as $key => $item) {
                    $sqlmenu .= "`ID` = " . $item['page'] . " OR ";
                }

                $sqlmenu = substr($sqlmenu,0,strlen($sqlmenu)-4);

                $sqlmenu .= " AND post_status = 'publish' ORDER BY menu_order ASC";

                if($stmtmenu = mysqli_prepare($wp, $sqlmenu)){
                    if(mysqli_stmt_execute($stmtmenu)){

                        $menu = [];
                        mysqli_stmt_bind_result($stmtmenu, $id, $post_title, $post_name, $menu_order);
                        while (mysqli_stmt_fetch($stmtmenu)) {
                            $menu[] = ["id" => $id, "title" => $post_title, "name" => $post_name, "menu" => $menu_order];
                        }

                        return $menu;
                    }
                }

            }
        }

    }

}

?>