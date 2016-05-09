# com_catalogo

La url final desde el menu es:
[categoria]/producto/[id]/[alias]
pero la del buscador queda asi:
component/catalogo/producto/[id]/[alias]
¿Como podría hacer para que quedarán igual?

Bueno, resulta que el problema con el router va más alla, a un producto puedo llegar desde un link a la categoria principal, el problema es que algunos productos estan en subcategorias, un ejemplo de categorias es:

- Accesorios y más
-- Accesorios
--- Cascos

Cuando se entra a Accesorios y más o a Accesorios se muestran todos los productos que estén en esas categorias, entonces, un producto que esté en Cascos me genera 3 links diferentes, uno con el alias de cada categoria :-S dependiendo de donde entre.

No se si me expliqué, pero para un solo producto tengo links tipo:
/accesorios-y-mas/producto/2/cross-035-fiber 
/accesorios/producto/2/cross-035-fiber
/accesorios/cascos/2/cross-035-fiber

Y a esto le agrego que en el plugin de search me genera todavia otro link diferente :-S
