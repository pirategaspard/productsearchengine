# productsearchengine

Project to learn Symfony framework, Twig, Doctrine, and data scraping. 

Instructions

create an empty db for the app and then
use composer to install the required libraries

Once app is running
Navigate to the admin area and click on "Add Source" to create a url starting point for the search engine.
Then click the link "Scan Next 5 Sources for New Product" to get some products. 

The app will scrape the next next 5 available urls and do the following:

If it finds a url, it will add it to the list of urls to be scanned

If it finds a "$" it will assume its a price, and create a product record using the price, page title, and the first image it finds on the page. 


