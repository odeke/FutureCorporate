			<div id="mws-navigation">
                <ul>
                    <li class="<?php echo $dashboard_pg; ?>"><a href="dashboard.php"><i class="icon-home"></i> Dashboard</a></li>
                    <li class="<?php echo $auction_new_pg . " " . $auction_history_pg; ?>">
                        <a href="#"><i class="icon-bullhorn"></i> Auctions</a>
                        <ul>
                            <li><a href="auction_new.php"><i class="icon-plus"></i> New Auction</a></li>
                            <li><a href="auction_history.php"><i class="icon-folder-closed"></i> My Auctions</a></li>
                        </ul>
                    </li>
                    <li class="<?php echo $rates_pg; ?>">
                        <a href="#"><i class="icon-graph"></i> Forex rates</a>
                        <ul>
                            <li><a href="rates.php"><i class="icon-home-2"></i> Local</a></li>
                            <li><a href="international.php"><i class="icon-globe"></i> International</a></li>
                        </ul>
                    </li>
                    <li class="<?php echo $bureaus_pg; ?>"><a href="bureaus.php"><i class="icon-home-3"></i> Forex Bureaus</a></li>
                    <li class="<?php echo $messages_pg; ?>">
                        <a href="messages.php"><i class="icon-envelope"></i> Messages</a><span class="mws-nav-tooltip">+<?php echo $totalRows_messages2; ?></span></li>
                    <li class="<?php echo $support_pg; ?>">
                        <a href="support.php">
                            <i class="icon-support"></i> 
                            Support <span class="mws-nav-tooltip">+<?php echo $totalRows_tickets_preview; ?></span>
                        </a>
                    </li>
                    <li class="<?php echo $my_account_pg; ?>"><a href="my_account.php"><i class="icon-user"></i> My Account</a></li>
                </ul>
            </div>