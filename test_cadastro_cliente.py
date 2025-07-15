from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.support.ui import Select, WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time

def digitar_devagar(elemento, texto, delay=0.1):
    for letra in texto:
        elemento.send_keys(letra)
        time.sleep(delay)

URL_CADASTRO = "http://localhost:8080/Iris_Essence_Xampp-main/html/cadastro_cliente.php"
caminho_chromedriver = "./chromedriver.exe"
service = Service(caminho_chromedriver)
driver = webdriver.Chrome(service=service)

try:
    driver.get(URL_CADASTRO)
    wait = WebDriverWait(driver, 10)

    print("Preenchendo nome...")
    nome_input = wait.until(EC.presence_of_element_located((By.ID, "nome")))
    digitar_devagar(nome_input, "Marina Ruy Barbosa")

    print("Preenchendo email...")
    email_input = wait.until(EC.presence_of_element_located((By.ID, "email")))
    digitar_devagar(email_input, "teste@biaaa.com")

    print("Preenchendo senha...")
    senha_input = wait.until(EC.presence_of_element_located((By.ID, "senha")))
    digitar_devagar(senha_input, "senha123")

    print("Preenchendo telefone...")
    telefone_input = wait.until(EC.presence_of_element_located((By.ID, "telefone")))
    telefone_input.clear()
    digitar_devagar(telefone_input, "11-87658-9853")  # robloooxx

    print("Preenchendo endereço...")
    endereco_input = wait.until(EC.presence_of_element_located((By.ID, "endereco")))
    digitar_devagar(endereco_input, "Rua das Flores, 123")

    print("Preenchendo data de nascimento...")
    wait.until(EC.presence_of_element_located((By.ID, "data_nascimento")))
    driver.execute_script("document.getElementById('data_nascimento').value = '2000-01-01';")
    time.sleep(1)  # pausa para garantir que o valor foi inserido e visualização

    print("Selecionando gênero...")
    select_genero = Select(wait.until(EC.presence_of_element_located((By.ID, "genero"))))
    select_genero.select_by_value("F")

    print("Clicando em salvar...")
    botao = wait.until(EC.element_to_be_clickable((By.CSS_SELECTOR, ".botao_cadastro")))
    botao.click()

    print("Esperando alerta...")
    alert = wait.until(EC.alert_is_present())
    print("Alerta recebido:", alert.text)
    time.sleep(2)  # pausa para leitura do alerta
    alert.accept()

    # Pausa final para inspecionar o que aconteceu antes de fechar o navegador
    input("Pressione Enter para fechar o navegador...")

except Exception as e:
    print("Deu erro:", e)

finally:
    print("Fechando o navegador")
    driver.quit()
