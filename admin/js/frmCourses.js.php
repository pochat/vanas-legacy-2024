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
  


function calcula_costo()
{
  if(isNaN(document.datos.app_fee.value))
  {
    alert('The value must be numeric');
    document.datos.app_fee.focus();
    return;
  }
  if(isNaN(document.datos.tuition.value))
  {
    alert('The value must be numeric');
    document.datos.tuition.focus();
    return;
  }
  if(isNaN(document.datos.no_costos_ad.value))
  {
    alert('The value must be numeric');
    document.datos.no_costos_ad.focus();
    return;
  }
  if(isNaN(document.datos.no_payments_a.value))
  {
    alert('The value must be numeric');
    document.datos.no_payments_a.focus();
    return;
  }
  if(isNaN(document.datos.interes_a.value))
  {
    alert('The value must be numeric');
    document.datos.interes_a.focus();
    return;
  }
  if(isNaN(document.datos.no_payments_b.value))
  {
    alert('The value must be numeric');
    document.datos.no_payments_b.focus();
    return;
  }
  if(isNaN(document.datos.interes_b.value))
  {
    alert('The value must be numeric');
    document.datos.interes_b.focus();
    return;
  }
  if(isNaN(document.datos.no_payments_c.value))
  {
    alert('The value must be numeric');
    document.datos.no_payments_c.focus();
    return;
  }
  if(isNaN(document.datos.interes_c.value))
  {
    alert('The value must be numeric');
    document.datos.interes_c.focus();
    return;
  }
  if(isNaN(document.datos.no_payments_d.value))
  {
    alert('The value must be numeric');
    document.datos.no_payments_d.focus();
    return;
  }
  if(isNaN(document.datos.interes_d.value))
  {
    alert('The value must be numeric');
    document.datos.interes_d.focus();
    return;
  }
  if(Trim(document.datos.no_costos_ad.value)>0 && Trim(document.datos.ds_costos_ad.value)=='')
  {
    alert('The additional costs description is required');
    document.datos.ds_costos_ad.focus();
    return;
  }
  if(Trim(document.datos.app_fee.value)=='')
  {
    alert('The Application Fee is required');
    document.datos.app_fee.focus();
    return;
  }
  if(Trim(document.datos.tuition.value)=='')
  {
    alert('The Tuition is required');
    document.datos.tuition.focus();
    return;
  }
  if(Trim(document.datos.no_payments_a.value)=='' || document.datos.no_payments_a.value<=0)
  {
    alert('The Number of payments for Option A is required');
    document.datos.no_payments_a.focus();
    return;
  }
  if(Trim(document.datos.frequency_a.value)=='')
  {
    alert('The Frequency for Option A is required');
    document.datos.frequency_a.focus();
    return;
  }
  if(Trim(document.datos.interes_a.value)=='')
  {
    alert('The Interest rate for Option A is required');
    document.datos.interes_a.focus();
    return;
  }
  
  document.datos.app_fee.value = MoneyFormat(Math.round(parseFloat(document.datos.app_fee.value)));
  document.datos.tuition.value = MoneyFormat(Math.round(parseFloat(document.datos.tuition.value)));
  document.datos.no_costos_ad.value = MoneyFormat(Math.round(parseFloat(document.datos.no_costos_ad.value)));
  if(Trim(document.datos.no_costos_ad.value)=='')
    document.datos.total_tuition.value = MoneyFormat(Math.round(parseFloat(document.datos.tuition.value)));
  else if(Trim(document.datos.no_costos_ad.value)!='')
    document.datos.total_tuition.value = MoneyFormat(Math.round(parseFloat(document.datos.tuition.value) + parseFloat(document.datos.no_costos_ad.value)));
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