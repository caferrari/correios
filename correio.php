<?php
/* 
 * Copyright (c) 2010, Carlos André Ferrari <[carlos@]ferrari.eti.br>
 * All rights reserved. 
 */

/**
 * Correio Class
 *
 * @author	Carlos André Ferrari <carlos@ferrari.eti.br>
 */
 
class Correio {

	public $status;
	public $hash;
	public $erro = false;
	public $track;

	/**
	* Construtor
	*
	* @param	string	$id		Código da encomenda
	* return void
	*/
	public function __construct($id=false){
		if ($id){
			if (strlen($id) == 13) $this->track ($id);
			else {
				$this->erro = true;
				$this->erro_msg = 'Código de encomenda Inválido!';
			}
		}
	}

	/**
	* Faz o rastreamendo da encomenda
	*
	* @param	string	$id		Código da encomenda
	* @return	void
	*/
	private function track($id){
		$html = utf8_encode(file_get_contents('http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=' . $id));

		// Verifica se o objeto ainda não foi postado, caso seja o caso, retorna erro e mensagem		
		if (strstr($html, '<table') === false){
			$this->erro = true;
			$this->erro_msg = 'Objeto ainda não foi adicionado no sistema';
			return;
		}

		// Hash para monitoramento de alteração de status
		$this->hash = md5($html);

		// Limpa o codigo html
		$html = preg_replace("@\r|\t|\n| +@", ' ', $html);
		$html = str_replace('</tr>', "</tr>\n", $html);

		// Pega as linhas com o rastreamento
		if (preg_match_all('@<tr>(.*)</tr>@', $html, $mat,PREG_SET_ORDER)){
			$track = array();
			$mat = array_reverse($mat);
			$temp = null;
			// Formata as linhas e gera um vetor
			foreach($mat as $item){
				if (preg_match("@<td rowspan=[12]>(.*)</td><td>(.*)</td><td><FONT COLOR=\"[0-9A-F]{6}\">(.*)</font></td>@", $item[0], $d)){
					// Cria uma linha de track
					$tmp = array(
						'data' => $d[1],
						'data_sql' => preg_replace('@([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-9]{2}):([0-9]{2})@', '$3-$2-$1 $4:$5:00',$d[1] ),
						'local' => $d[2],
						'acao' => strtolower($d[3]),
						'detalhes' => ''
					);

					// Se tiver um encaminhamento armazenado
					if ($temp){
						$tmp['detalhes'] = $temp;
						$temp = null;
					}

					// Adiciona o item na lista de rastreamento
					$track[] = (object)$tmp;
				}else if (preg_match("@<td colspan=2>(.*)</td>@", $item[0], $d)){
					// Se for um encaminhamento, armazena para o proximo item
					$temp = $d[1];
				}
				$this->status = $tmp['acao'];
			}
			$this->track = $track;
			return;
		}

		// Caso retorne um html desconhecido ou falhe, retorna erro de comunicação
		$this->erro = true;
		$this->erro_msg = 'Falha de Comunicação com os correios';
	}
}
