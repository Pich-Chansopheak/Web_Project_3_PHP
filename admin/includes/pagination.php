<?php
    $p=$_GET['p'];
    
    if($numpage>1)
    {
?>
    <!-- pagination -->
    <nav aria-label="Page navigation example" class="mt-2">
              <ul class="pagination justify-content-center">
                <li class="page-item"><a class="page-link" href="index.php?p=<?=$p?>&pg=<?=($pg>1?$pg-1:1)?>">Previous</a></li>
            <?php
                for($i=1;$i<=$numpage;$i++){
            ?>
                <li class="page-item <?=$pg==$i?"active":""?>"><a class="page-link" href="index.php?p=<?=$p?>&pg=<?=$i?>"><?= $i?></a></li>
            <?php
                }
            ?>
                <li class="page-item"><a class="page-link" href="index.php?p=<?=$p?>&pg=<?=($pg<$numpage?$pg+1:$numpage)?>">Next</a></li>
              </ul>
    </nav>
    <!-- End of pagination -->
<?php
    }
?>