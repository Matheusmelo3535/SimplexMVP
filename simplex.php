<?php

$tabelaInicial = [
    [1, -3, -2, 0, 0, 0, 0],
    [0, 2, 1, 1, 0, 0, 18],
    [0, 2, 3, 0, 1, 0, 42],
    [0, 3, 1, 0, 0, 1, 24]
];

$elementosColunaQueEntra = [];

function sortElementosAsc($element1, $element2) {
    return ($element1 > $element2) ? -1 : 1;
}

function sortElementosDesc($element1, $element2) {
    return ($element1 < $element2) ? -1 : 1;
}

function findColunaQueEntra($tabela) {
    $elementosLinhaBaseSort = array();
    foreach ($tabela[0] as $key => $elemento) {
        if ($elemento < 0 ) {
            $elementosLinhaBaseSort[$key] = $elemento;
        }
    }
    $elementosLinhaSemSort = $elementosLinhaBaseSort;
    uasort($elementosLinhaBaseSort, "sortElementosAsc");
   
    $indexColunaQueEntra = array_key_last($elementosLinhaBaseSort);
    
    return $indexColunaQueEntra;
}

function findLinhaPivo($tabela, $indexColunaEntra) {
    $elementosColunaEntra = [];
    foreach ($tabela as $linha) {
        array_push($elementosColunaEntra, $linha[$indexColunaEntra]);
    }
    // remove o elemento da linha base, pois ele não irá ser utilizado nessa etapa
    $qtdLinhasTabela = count($tabela) - 1;
    $resultadoDivisoes = [];
    for ($i = 1; $i <= $qtdLinhasTabela; $i++) {
        $variavelFolga = end($tabela[$i]);
        $resultadoDivisoes[$i] = $variavelFolga / $elementosColunaEntra[$i];
    }

    uasort($resultadoDivisoes, "sortElementosDesc");
    $linhaDoPivo = array_key_first($resultadoDivisoes);
    $pivo = $tabela[$linhaDoPivo][$indexColunaEntra];
    $retorno = ["pivo" => $pivo, "linhaDoPivo" => $linhaDoPivo, "elementosColunaEntra" => $elementosColunaEntra];
    
    return $retorno;
}


function novaLinhaPivo($tabela, $pivoElinha) {
    $pivo = $pivoElinha["pivo"];
    $linhaPivo = $pivoElinha["linhaDoPivo"];
    $novaLinhaPivo = [];
    
    foreach ($tabela[$linhaPivo] as $elemento) {
        $novaLinhaPivo[] = $elemento / $pivo;
    }
    $tabela[$linhaPivo] = $novaLinhaPivo;
    
    $retorno = ["novaTabela" => $tabela, "linhaPivo" => $linhaPivo, "elementosColunaEntra" => $pivoElinha["elementosColunaEntra"]];
    
    return $retorno;
}

function refazerLinhas($tabelaElinhaPivo) {
    $linhaPivo = $tabelaElinhaPivo["linhaPivo"];
    $tabela = $tabelaElinhaPivo["novaTabela"];
    $elementosColunaEntra = $tabelaElinhaPivo["elementosColunaEntra"];
    $qtdLinhasTabela = count($tabela);
    $nlp = $tabelaElinhaPivo["novaTabela"][$linhaPivo];
    $qtdElementosNlp = count($nlp);
    
    for ($i = 0; $i < $qtdLinhasTabela; $i++){
        if ($i == $linhaPivo) {
            continue;
        }
       $elementoDaLinha = $elementosColunaEntra[$i] * -1;
       $linhaPraSomar = [];
       foreach ($nlp as $elementoNlp) {
            $linhaPraSomar[] = $elementoNlp * $elementoDaLinha;
            
       }
       
      for ($j = 0; $j < $qtdElementosNlp; $j++) {
        $tabela[$i][$j] += $linhaPraSomar[$j];
      }
    }
    
    return $tabela;
}

$iteracao1 = simplex($tabelaInicial);
$iteracao2 = simplex($iteracao1);
$iteracao3 = simplex($iteracao2);
// $iteracao4 = simplex($iteracao3);
// $iteracao5 = simplex($iteracao4);


function simplex($tabelaInicial) {
    $indexColunaQueEntra = findColunaQueEntra($tabelaInicial);
    $elementosLinhaPivo = findLinhaPivo($tabelaInicial, $indexColunaQueEntra);
    $novaLinhaPivo = novaLinhaPivo($tabelaInicial, $elementosLinhaPivo);
    $tabelaRefeita = refazerLinhas($novaLinhaPivo);
    $solucaoOtima = solucaoOtimaEncontrada($tabelaRefeita);
   
    // // while ($solucaoOtima == false) {
    //     $indexColunaQueEntra = findColunaQueEntra($tabelaRefeita);
    //     $elementosLinhaPivo = findLinhaPivo($tabelaRefeita, $indexColunaQueEntra);
    //     $novaLinhaPivo = novaLinhaPivo($tabelaRefeita, $elementosLinhaPivo);
    //     $tabelaRefeita = refazerLinhas($novaLinhaPivo);
    //     $solucaoOtima = solucaoOtimaEncontrada($tabelaRefeita);
    //     print_r($tabelaRefeita);
        
    // // }

    return $tabelaRefeita;
}

function solucaoOtimaEncontrada($tabela) {
    $linhaBase = $tabela[0];
    $todosValoresPositivos = true;
    foreach ($linhaBase as $coluna) {
        if ($coluna < 0) {
            $todosValoresPositivos = false;
            return $todosValoresPositivos;
        }
    }
    
    return $todosValoresPositivos;
}


