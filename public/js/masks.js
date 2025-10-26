// Funções de máscara
function mascaraCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    return cpf;
}

function mascaraCNPJ(cnpj) {
    cnpj = cnpj.replace(/\D/g, '');
    cnpj = cnpj.replace(/^(\d{2})(\d)/, '$1.$2');
    cnpj = cnpj.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
    cnpj = cnpj.replace(/\.(\d{3})(\d)/, '.$1/$2');
    cnpj = cnpj.replace(/(\d{4})(\d)/, '$1-$2');
    return cnpj;
}

function mascaraTelefone(telefone) {
    telefone = telefone.replace(/\D/g, '');
    if (telefone.length === 11) {
        telefone = telefone.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (telefone.length === 10) {
        telefone = telefone.replace(/^(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
    } else {
        telefone = telefone.replace(/^(\d{2})(\d{4,5})(\d{4})/, '($1) $2-$3');
    }
    return telefone;
}

function mascaraCEP(cep) {
    cep = cep.replace(/\D/g, '');
    cep = cep.replace(/^(\d{5})(\d)/, '$1-$2');
    return cep;
}

// Aplicar máscaras automaticamente
document.addEventListener('DOMContentLoaded', function() {
    // Máscara para CPF
    const cpfInputs = document.querySelectorAll('input[name*="cpf"], input[name*="CPF"]');
    cpfInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = mascaraCPF(e.target.value);
        });
    });

    // Máscara para CNPJ
    const cnpjInputs = document.querySelectorAll('input[name*="cnpj"], input[name*="CNPJ"]');
    cnpjInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = mascaraCNPJ(e.target.value);
        });
    });

    // Máscara para Telefone
    const telefoneInputs = document.querySelectorAll('input[name*="telefone"], input[name*="phone"], input[name*="celular"]');
    telefoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = mascaraTelefone(e.target.value);
        });
    });

    // Máscara e busca de CEP
    const cepInputs = document.querySelectorAll('input[name*="cep"], input[name*="CEP"]');
    cepInputs.forEach(input => {
        // Aplicar máscara
        input.addEventListener('input', function(e) {
            e.target.value = mascaraCEP(e.target.value);
        });

        // Buscar CEP quando perder o foco (se tiver 9 caracteres)
        input.addEventListener('blur', function(e) {
            const cep = e.target.value.replace(/\D/g, '');
            if (cep.length === 8) {
                buscarCEP(cep);
            }
        });
    });
});

// Função para buscar CEP via API ViaCEP
function buscarCEP(cep) {
    // Mostrar loading
    const enderecoFields = document.querySelectorAll('input[name="logradouro"], input[name="numero"], input[name="bairro"], input[name="cidade"], select[name="estado"]');
    enderecoFields.forEach(field => {
        field.disabled = true;
        field.placeholder = 'Buscando...';
    });

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            if (!data.erro) {
                // Preencher os campos automaticamente
                if (document.querySelector('input[name="logradouro"]')) {
                    document.querySelector('input[name="logradouro"]').value = data.logradouro || '';
                }
                if (document.querySelector('input[name="bairro"]')) {
                    document.querySelector('input[name="bairro"]').value = data.bairro || '';
                }
                if (document.querySelector('input[name="cidade"]')) {
                    document.querySelector('input[name="cidade"]').value = data.localidade || '';
                }
                if (document.querySelector('select[name="estado"]')) {
                    document.querySelector('select[name="estado"]').value = data.uf || '';
                }

                // Focar no campo número para o usuário completar
                if (document.querySelector('input[name="numero"]')) {
                    document.querySelector('input[name="numero"]').focus();
                }
            } else {
                alert('CEP não encontrado. Por favor, verifique o CEP digitado.');
            }
        })
        .catch(error => {
            console.error('Erro ao buscar CEP:', error);
            alert('Erro ao buscar CEP. Tente novamente.');
        })
        .finally(() => {
            // Reativar campos
            enderecoFields.forEach(field => {
                field.disabled = false;
                field.placeholder = '';
            });
        });
}

// Função para formatar valor monetário
function mascaraMoeda(input) {
    let value = input.value.replace(/\D/g, '');
    value = (value / 100).toFixed(2) + '';
    value = value.replace(".", ",");
    value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
    value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
    input.value = value;
}

// Aplicar máscara monetária
document.addEventListener('DOMContentLoaded', function() {
    const moneyInputs = document.querySelectorAll('input[type="number"][step="0.01"]');
    moneyInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            // Formatar como moeda brasileira
            let value = e.target.value.replace(/\D/g, '');
            value = (value / 100).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            e.target.value = value;
        });
    });
});
