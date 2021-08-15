 
//<?php
/**
 * ArendaKass
 *
 * Отправка чеков в сервис arendakass.ru
 *
 * @category    plugin
 * @version     1.0.0
 * @author      Pathologic
 * @internal    @events OnOrderPaid
 * @internal    @properties &token=Токен;text; &mode=Режим;list;Demo==demo||Api==api;demo &name=Название продавца;text; &inn=ИНН продавца;text; &tax=Налог;list;НДС 20%==1||НДС 10%==2||НДС 0%==3||Без НДС==4||НДС 20/120==5||НДС 10/110==6;4
 * @internal    @modx_category Commerce
 */

return require MODX_BASE_PATH . 'assets/plugins/arendakass/plugin.arendakass.php';
