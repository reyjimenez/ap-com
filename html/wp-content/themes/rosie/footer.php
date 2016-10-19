        <div class="clear"></div>
        
        </div><!--.container-->    
    
        <?php
            /* footer slider */
            global $ozy_data;
            ozy_put_footer_slider($ozy_data->footer_slider);
            
            /*footer widget bar and footer*/
            include('include/footer-bars.php');
            
            /*post side navigation bars*/
            include('include/single-post-navigation.php'); 
        ?>
        
    </div><!--#main-->
  	
    <?php wp_footer(); ?>

</body>
</html>