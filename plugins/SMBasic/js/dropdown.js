/* 
 *  Copyright @ 2016 - 2019 Diego Garcia
 */

function dropdown(element)
{
    vista = document.getElementById(element).style.display;
    if (vista === 'none')
        vista = 'block';
    else
        vista = 'none';

    document.getElementById(element).style.display = vista;
}