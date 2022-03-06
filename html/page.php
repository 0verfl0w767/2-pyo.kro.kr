<html>
    <head>
        <title>2pyo page</title>
    </head>
    <body>
        <?php
        include './utils/DataManager.php';
        include './utils/MainLogger.php';

        $logger = new utils\MainLogger();
        $logger::setServerPath('/root/2pyo');

        $page = new utils\DataManager($logger::getServerPath() . '/datas/page.json', utils\DataManager::JSON);
        $db['page'] = $page->getAll();

        $title = $db['page'][$argv[1]]['title'];
        $vote = $db['page'][$argv[1]]['vote'];

        if (isset($argv[2])){

            echo '<label>투표 주제: ' . $title . '</label><br>';

            foreach($vote as $votes => $count){

                echo '<label>' . $votes .' : ' . $count . '표</label><br>';

            }

            return;

        }

        echo '<form action="/page/' . $argv[1] . '" method="POST">';
        echo '<label>투표 주제: ' . $title . '</label><br>';
        foreach($vote as $votes => $count){
            echo '<input type="radio" name="vote" value="' . $votes . '">' . $votes . '<br>';
        }
        echo '<input type="submit" id="page" value="투표">';
        echo '</form>';
        ?>
    </body>
</html>