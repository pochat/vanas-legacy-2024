  function Ltrim(sString) 
  {
    while (sString.substring(0,1) == ' ') 
    {
      sString = sString.substring(1, sString.length);
    }
    return sString;
  }
  
  function Rtrim(TextToTrim) 
  {
    while (TextToTrim.substring(TextToTrim.length-1, TextToTrim.length) == ' ') 
    {
      TextToTrim = TextToTrim.substring(0,TextToTrim.length-1);
    }
    return TextToTrim;
  }
  
  function Trim(strInput) 
  {
    return Ltrim( Rtrim(strInput))
  }
  


function calcula()
{
  if(isNaN(document.datos.no_costos_ad.value))
  {
    alert('The value must be numeric');
    document.datos.no_costos_ad.focus();
    return;
  }
  if(isNaN(document.datos.no_descuento.value))
  {
    alert('The value must be numeric');
    document.datos.no_descuento.focus();
    return;
  }
  
  document.datos.no_costos_ad.value = MoneyFormat(Math.round(parseFloat(document.datos.no_costos_ad.value)));  
  document.datos.no_descuento.value = MoneyFormat(Math.round(parseFloat(document.datos.no_descuento.value)));  
  if((Trim(document.datos.no_costos_ad.value)=='' && Trim(document.datos.no_descuento.value)=='') || 
     (Trim(document.datos.no_costos_ad.value)==0 && Trim(document.datos.no_descuento.value)==0))
    document.datos.total_tuition.value = MoneyFormat(Math.round(parseFloat(document.datos.tuition.value)));
  else if((Trim(document.datos.no_costos_ad.value)!='' || Trim(document.datos.no_costos_ad.value)!=0) && 
          (Trim(document.datos.no_descuento.value)=='' || Trim(document.datos.no_descuento.value)==0))
    document.datos.total_tuition.value = MoneyFormat(Math.round(parseFloat(document.datos.tuition.value) + parseFloat(document.datos.no_costos_ad.value)));
  else if((Trim(document.datos.no_costos_ad.value)=='' || Trim(document.datos.no_costos_ad.value)==0) && 
          (Trim(document.datos.no_descuento.value)!='' || Trim(document.datos.no_descuento.value)!=0))
    document.datos.total_tuition.value = MoneyFormat(Math.round(parseFloat(document.datos.tuition.value) - document.datos.no_descuento.value));
  else if((Trim(document.datos.no_costos_ad.value)!='' || Trim(document.datos.no_costos_ad.value)!=0) && 
          (Trim(document.datos.no_descuento.value)!='' || Trim(document.datos.no_descuento.value)!=0))
    document.datos.total_tuition.value = MoneyFormat(Math.round(parseFloat(document.datos.tuition.value) + parseFloat(document.datos.no_costos_ad.value) - document.datos.no_descuento.value));
  document.datos.total.value = MoneyFormat(Math.round(parseFloat(document.datos.app_fee.value) + parseFloat(document.datos.total_tuition.value)));
  if(document.datos.interes_a.value>0)
    int_a = (parseFloat(document.datos.interes_a.value)/100)+1;
  else
    int_a = 1;
  document.datos.amount_due_a.value = MoneyFormat(Math.round(parseFloat(document.datos.total_tuition.value)*int_a/parseFloat(document.datos.no_payments_a.value)));
  document.datos.amount_paid_a.value = MoneyFormat(Math.round(parseFloat(document.datos.amount_due_a.value) * parseFloat(document.datos.no_payments_a.value)));
  
  if(document.datos.no_payments_b.value>0 && document.datos.interes_b.value>0)
  {
    int_b = (parseFloat(document.datos.interes_b.value)/100)+1;
    document.datos.amount_due_b.value = MoneyFormat(Math.round(parseFloat(document.datos.total_tuition.value)*int_b/parseFloat(document.datos.no_payments_b.value)));
    document.datos.amount_paid_b.value = MoneyFormat(Math.round(parseFloat(document.datos.amount_due_b.value) * parseFloat(document.datos.no_payments_b.value)));
  }
  else
  {
    document.datos.amount_due_b.value = 0;
    document.datos.amount_paid_b.value = 0;
  }
  
  if(document.datos.no_payments_c.value>0 && document.datos.interes_c.value>0)
  {
    int_c = (parseFloat(document.datos.interes_c.value)/100)+1;
    document.datos.amount_due_c.value = MoneyFormat(Math.round(parseFloat(document.datos.total_tuition.value)*int_c/parseFloat(document.datos.no_payments_c.value)));
    document.datos.amount_paid_c.value = MoneyFormat(Math.round(parseFloat(document.datos.amount_due_c.value) * parseFloat(document.datos.no_payments_c.value)));
  }
  else
  {
    document.datos.amount_due_c.value = 0;
    document.datos.amount_paid_c.value = 0;
  }
  
  if(document.datos.no_payments_d.value>0 && document.datos.interes_d.value>0)
  {
    int_d = (parseFloat(document.datos.interes_d.value)/100)+1;
    document.datos.amount_due_d.value = MoneyFormat(Math.round(parseFloat(document.datos.total_tuition.value)*int_d/parseFloat(document.datos.no_payments_d.value)));
    document.datos.amount_paid_d.value = MoneyFormat(Math.round(parseFloat(document.datos.amount_due_d.value) * parseFloat(document.datos.no_payments_d.value)));
  }
  else
  {
    document.datos.amount_due_d.value = 0;
    document.datos.amount_paid_d.value = 0;
  }
}

  function MoneyFormat(num) 
  { 
    num = num.toString().replace(/\$|\,/g,'');
    if(isNaN(num))
    num = "0";
    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num*100+0.50000000001);
    cents = num%100;
    num = Math.floor(num/100).toString();
    if(cents<10)
    cents = "0" + cents;
    for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
    num = num.substring(0,num.length-(4*i+3))+
    num.substring(num.length-(4*i+3));
    return (((sign)?'':'-') + num + '.' + cents);
  }