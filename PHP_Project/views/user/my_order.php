<?php
/**
 * /my-orders redirects to /orders (canonical order-history URL)
 */
header('Location: /orders', true, 302);
exit;
