<div class="side-menu animate-dropdown outer-bottom-xs">
    <div class="head"><i class="icon fa fa-align-justify fa-fw"></i> Categories</div>
    <nav class="yamm megamenu-horizontal" role="navigation">

        <ul class="nav">
            <li class="dropdown menu-item">
                <?php $sql = mysqli_query($con, "select id,categoryName  from category");
                while ($row = mysqli_fetch_array($sql)) {
                ?>
                    <a href="category.php?cid=<?php echo $row['id']; ?>" class="dropdown-toggle"><i class="icon fa fa-desktop fa-fw"></i>
                        <?php 
                    //Test decryption
                     $key='qkwjdiw239&&jdafweihbrhnan&^%$ggdnawhd4njshjwuuO';
                    $encryption_key = base64_decode($key);
                    list($encrypted_data, $iv) = array_pad(explode('::', base64_decode($row['categoryName']), 2),2,null);
                    $row['categoryName'] = openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
                    
                        
                        echo $row['categoryName']; ?></a>
                <?php } ?>

            </li>
        </ul>
    </nav>
</div>