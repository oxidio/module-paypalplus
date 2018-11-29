==Title==
PayPal Plus

==Author==
PayPal (Europe) S.à r.l. et Cie, S.C.A.

==Prefix==
payp

==Shop Version==
6.0.x

==Version==
3.0.x

==Link==
https://www.paypal.com

==Mail==
service@paypal.com

==Description==
PayPal Plus payments module for OXID eShop

==Installation==
 * Copy files from copy_this/ folder to eShop root directory
 * Activate PayPal Plus module in administration back end: under "Extensions -> Modules -> PayPal Plus", tab "Overview" press "Activate" button
 * Enter PayPal API Client ID and Secret key and adjust other settings in "Extensions -> Modules -> PayPal Plus", tab "Settings"
 * Optionally configure eShop shipping methods and shipping cost rules

==Extend==
 * language_main
    -- save
 * order_list
    -- render
    -- _prepareWhereQuery
    -- _buildSelectString
    -- _prepareOrderByQuery
 * basket
    -- render
 * order
    -- init
 * payment
    -- render
    -- validatePayment
    -- getPaymentErrorText
 * oxviewconfig
 * oxAddress
    -- save
 * oxBasket
    -- afterUpdate
    -- getPaymentCost
    -- getTotalDiscountSum
    -- getTsProductId
 * oxOrder
    -- save
    -- delete
 * oxPaymentGateway
    -- executePayment
 * oxUser
     -- save
 * thankyou
    -- init
    -- render     

==Modules==

==Modified original templates==

==Uninstall==
 * Disable the module in administration area
 * Delete module folder
 * Optionally, execute docs/uninstall.sql on the database to remove PayPal Plus related entries
