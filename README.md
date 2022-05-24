# Centarro test module

## Task description
Product prices should vary based on a period of time. Periods are fixed:

- 00:00 – 08:00
- 08:00 – 16:00
- 16:00 – 00:00

If no period is set, product should use default price
Show prices based on time period on product page.
Administrator should be able to adjust/changes prices per period.
Administrator should be able to mark specific period as free shipping. If product is purchased in that period there is no shipping cost.

## What I have done:
- Provided new price fields through EntityTrait. It means that we can enable/disable them on the 'Product variation types' config form.
- Made a new custom view field 'Price by period'. This field returns the price according to the day period or default one.
- Made a new commerce condition 'Day period' which allow to configure the rules of the shipping methods.

## P.S.
If we need always retrieve the correct price from the Product variation entity, we can use the other variant of the module.

See PR - https://github.com/devbranch-vitaliy/Centarro_test_module/pull/1

This approach is not recommended because we override the entity class. But it allows us to redefine the getPrice() function. 
