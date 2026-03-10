#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
==========================================================
Livrable2.py
Validation automatique
==========================================================

Description
-----------
Script exécuté dans le dossier du projet étudiant.

Génère :
- _reports/Livrable2.txt
- _reports/Livrable2.json

La sortie console est identique au fichier TXT.

Groupes actuels :
- STRUCTURE
- STRUCTURE PHP
- INCLUDE_CSS
- BOOTSTRAP

Chaque critère vaut 1 point par défaut.

Auteur : AutoValidator
Date : 2026-03-10

Pré-requis
----------
Python 3.9+

Librairies utilisées
--------------------
Aucune librairie externe.
Bibliothèque standard uniquement :
- os
- json
- traceback
- datetime

Usage
-----
python Livrable2.py
"""

import os
import json
import traceback
from datetime import datetime


LIVRABLE_NAME = "Livrable2"
REPORT_DIR = "_reports"


class Logger:
    """Gère l'affichage console et l'export TXT identique."""

    def __init__(self):
        self.lines = []

    def write(self, text=""):
        print(text)
        self.lines.append(text)

    def save_txt(self, path):
        with open(path, "w", encoding="utf-8") as file:
            for line in self.lines:
                file.write(line + "\n")


class JsonReport:
    """Construit le rapport JSON final."""

    def __init__(self, student_folder, student_path):
        self.data = {
            "schema_version": "1.0",
            "generated_at": datetime.utcnow().isoformat() + "Z",
            "student": {
                "folder": student_folder,
                "path": student_path
            },
            "criteria": {},
            "totals": {
                "pts": 0,
                "max": 0
            }
        }

    def add(self, key, group, label, ok, message=""):
        pts = 1 if ok else 0

        self.data["criteria"][key] = {
            "group": group,
            "label": label,
            "pts": pts,
            "max": 1,
            "message": message
        }

        self.data["totals"]["pts"] += pts
        self.data["totals"]["max"] += 1

    def save(self, path):
        with open(path, "w", encoding="utf-8") as file:
            json.dump(self.data, file, indent=2, ensure_ascii=False)


def read_file(path):
    """Lit un fichier texte UTF-8. Retourne None si lecture impossible."""
    try:
        with open(path, "r", encoding="utf-8") as file:
            return file.read()
    except Exception:
        return None


def file_exists(path):
    return os.path.isfile(path)


def dir_exists(path):
    return os.path.isdir(path)


def count_occurrence(text, word):
    if text is None:
        return 0
    return text.count(word)


def contains_any(text, words):
    """Retourne True si au moins un mot est trouvé dans le texte."""
    if text is None:
        return False

    lower_text = text.lower()
    for word in words:
        if word.lower() in lower_text:
            return True
    return False


def check_partials(content):
    """
    Vérifie qu'un fichier PHP racine référence exactement une fois
    chacun des 4 partials requis.
    """
    partials = [
        "_head.php",
        "_navbar.php",
        "_footer.php",
        "_libjs.php"
    ]

    for partial in partials:
        count = count_occurrence(content, partial)
        if count != 1:
            return False, f"{partial} manquant ou multiple"

    return True, ""


def write_group_separator(logger):
    logger.write("")
    logger.write("--------------")
    logger.write("")


def test_file(project_path, rel_path, logger, report):
    key = "file_" + rel_path.replace("/", "_").replace("\\", "_")
    path = os.path.join(project_path, rel_path)

    if file_exists(path):
        logger.write(f"* {rel_path} : OK")
        report.add(key, "STRUCTURE", rel_path, True)
    else:
        logger.write(f"* {rel_path} : FAIL")
        logger.write("    - manquant")
        report.add(key, "STRUCTURE", rel_path, False, "manquant")


def test_dir(project_path, rel_path, logger, report):
    key = "dir_" + rel_path.replace("/", "_").replace("\\", "_")
    path = os.path.join(project_path, rel_path)

    if dir_exists(path):
        logger.write(f"* {rel_path} : OK")
        report.add(key, "STRUCTURE", rel_path, True)
    else:
        logger.write(f"* {rel_path} : FAIL")
        logger.write("    - manquant")
        report.add(key, "STRUCTURE", rel_path, False, "manquant")


def validate_structure(project_path, logger, report):
    logger.write("## STRUCTURE")

    test_file(project_path, "index.php", logger, report)
    test_file(project_path, "view-user.php", logger, report)
    test_file(project_path, "view-admin.php", logger, report)

    test_dir(project_path, "data", logger, report)

    test_file(project_path, "partials/_head.php", logger, report)
    test_file(project_path, "partials/_navbar.php", logger, report)
    test_file(project_path, "partials/_footer.php", logger, report)
    test_file(project_path, "partials/_libjs.php", logger, report)

    test_file(project_path, "src/JsonRepository.php", logger, report)

    test_file(project_path, "assets/css/styles.css", logger, report)
    test_dir(project_path, "assets/img", logger, report)

    write_group_separator(logger)


def check_php_file(project_path, filename, logger, report):
    key = "php_" + filename.replace(".", "_")
    label = f"Critere import php ({filename})"
    path = os.path.join(project_path, filename)

    try:
        if not file_exists(path):
            logger.write(f"* {label} : FAIL")
            logger.write("    - fichier manquant")
            report.add(
                key,
                "STRUCTURE PHP",
                label,
                False,
                "fichier manquant"
            )
            return

        content = read_file(path)
        ok, message = check_partials(content)

        if ok:
            logger.write(f"* {label} : OK")
            report.add(key, "STRUCTURE PHP", label, True)
        else:
            logger.write(f"* {label} : FAIL")
            logger.write(f"    - {message}")
            report.add(key, "STRUCTURE PHP", label, False, message)

    except Exception as exc:
        logger.write(f"* {label} : FAIL")
        logger.write(f"    - erreur: {exc}")
        report.add(key, "STRUCTURE PHP", label, False, str(exc))


def validate_php_structure(project_path, logger, report):
    logger.write("## STRUCTURE PHP")

    check_php_file(project_path, "index.php", logger, report)
    check_php_file(project_path, "view-user.php", logger, report)
    check_php_file(project_path, "view-admin.php", logger, report)

    write_group_separator(logger)


def check_styles_not_in_root_file(project_path, filename, logger, report):
    """
    styles.css ne doit pas être inclus directement dans un fichier PHP racine.
    """
    key = "include_css_shared_" + filename.replace(".", "_")
    label = f"Critère style.css partagé ({filename})"
    path = os.path.join(project_path, filename)

    try:
        if not file_exists(path):
            logger.write(f"* {label} : FAIL")
            logger.write("    - fichier manquant")
            report.add(key, "INCLUDE_CSS", label, False, "fichier manquant")
            return

        content = read_file(path)

        if contains_any(content, ["styles.css"]):
            logger.write(f"* {label} : FAIL")
            logger.write("    - styles.css ne doit pas être inclus dans ce fichier")
            report.add(
                key,
                "INCLUDE_CSS",
                label,
                False,
                "styles.css ne doit pas être inclus dans ce fichier"
            )
        else:
            logger.write(f"* {label} : OK")
            report.add(key, "INCLUDE_CSS", label, True)

    except Exception as exc:
        logger.write(f"* {label} : FAIL")
        logger.write(f"    - erreur: {exc}")
        report.add(key, "INCLUDE_CSS", label, False, str(exc))


def check_styles_in_head(project_path, logger, report):
    """
    styles.css doit être présent dans partials/_head.php.
    """
    key = "include_css_head"
    label = "Critère style.css dans _head.php"
    path = os.path.join(project_path, "partials", "_head.php")

    try:
        if not file_exists(path):
            logger.write(f"* {label} : FAIL")
            logger.write("    - partials/_head.php manquant")
            report.add(key, "INCLUDE_CSS", label, False, "partials/_head.php manquant")
            return

        content = read_file(path)

        if contains_any(content, ["styles.css"]):
            logger.write(f"* {label} : OK")
            report.add(key, "INCLUDE_CSS", label, True)
        else:
            logger.write(f"* {label} : FAIL")
            logger.write("    - styles.css absent de partials/_head.php")
            report.add(
                key,
                "INCLUDE_CSS",
                label,
                False,
                "styles.css absent de partials/_head.php"
            )

    except Exception as exc:
        logger.write(f"* {label} : FAIL")
        logger.write(f"    - erreur: {exc}")
        report.add(key, "INCLUDE_CSS", label, False, str(exc))


def validate_include_css(project_path, logger, report):
    logger.write("## INCLUDE_CSS")

    check_styles_not_in_root_file(project_path, "index.php", logger, report)
    check_styles_not_in_root_file(project_path, "view-user.php", logger, report)
    check_styles_not_in_root_file(project_path, "view-admin.php", logger, report)

    check_styles_in_head(project_path, logger, report)

    write_group_separator(logger)


def check_bootstrap_css_not_in_root_file(project_path, filename, logger, report):
    """
    bootstrap.css / bootstrap.min.css ne doit pas être inclus directement
    dans un fichier PHP racine.
    """
    key = "bootstrap_css_shared_" + filename.replace(".", "_")
    label = f"Critère bootstrap.css partagé ({filename})"
    path = os.path.join(project_path, filename)

    try:
        if not file_exists(path):
            logger.write(f"* {label} : FAIL")
            logger.write("    - fichier manquant")
            report.add(key, "BOOTSTRAP", label, False, "fichier manquant")
            return

        content = read_file(path)

        if contains_any(content, ["bootstrap.min.css", "bootstrap.css"]):
            logger.write(f"* {label} : FAIL")
            logger.write("    - bootstrap.css ne doit pas être inclus dans ce fichier")
            report.add(
                key,
                "BOOTSTRAP",
                label,
                False,
                "bootstrap.css ne doit pas être inclus dans ce fichier"
            )
        else:
            logger.write(f"* {label} : OK")
            report.add(key, "BOOTSTRAP", label, True)

    except Exception as exc:
        logger.write(f"* {label} : FAIL")
        logger.write(f"    - erreur: {exc}")
        report.add(key, "BOOTSTRAP", label, False, str(exc))


def check_bootstrap_js_not_in_root_file(project_path, filename, logger, report):
    """
    bootstrap.js / bootstrap.min.js ne doit pas être inclus directement
    dans un fichier PHP racine.
    """
    key = "bootstrap_js_shared_" + filename.replace(".", "_")
    label = f"Critère bootstrap.js partagé ({filename})"
    path = os.path.join(project_path, filename)

    try:
        if not file_exists(path):
            logger.write(f"* {label} : FAIL")
            logger.write("    - fichier manquant")
            report.add(key, "BOOTSTRAP", label, False, "fichier manquant")
            return

        content = read_file(path)

        if contains_any(content, ["bootstrap.min.js", "bootstrap.js"]):
            logger.write(f"* {label} : FAIL")
            logger.write("    - bootstrap.js ne doit pas être inclus dans ce fichier")
            report.add(
                key,
                "BOOTSTRAP",
                label,
                False,
                "bootstrap.js ne doit pas être inclus dans ce fichier"
            )
        else:
            logger.write(f"* {label} : OK")
            report.add(key, "BOOTSTRAP", label, True)

    except Exception as exc:
        logger.write(f"* {label} : FAIL")
        logger.write(f"    - erreur: {exc}")
        report.add(key, "BOOTSTRAP", label, False, str(exc))


def check_bootstrap_css_in_head(project_path, logger, report):
    """
    bootstrap.min.css doit être présent dans partials/_head.php.
    """
    key = "bootstrap_css_head"
    label = "Critere bootstrap.css _head.php"
    path = os.path.join(project_path, "partials", "_head.php")

    try:
        if not file_exists(path):
            logger.write(f"* {label} : FAIL")
            logger.write("    - partials/_head.php manquant")
            report.add(key, "BOOTSTRAP", label, False, "partials/_head.php manquant")
            return

        content = read_file(path)

        if contains_any(content, ["bootstrap.min.css"]):
            logger.write(f"* {label} : OK")
            report.add(key, "BOOTSTRAP", label, True)
        else:
            logger.write(f"* {label} : FAIL")
            logger.write("    - bootstrap.min.css absent de partials/_head.php")
            report.add(
                key,
                "BOOTSTRAP",
                label,
                False,
                "bootstrap.min.css absent de partials/_head.php"
            )

    except Exception as exc:
        logger.write(f"* {label} : FAIL")
        logger.write(f"    - erreur: {exc}")
        report.add(key, "BOOTSTRAP", label, False, str(exc))


def check_bootstrap_js_in_libjs(project_path, logger, report):
    """
    bootstrap.min.js doit être présent dans partials/_libjs.php.
    """
    key = "bootstrap_js_libjs"
    label = "Critere bootstrap.js _libjs.php"
    path = os.path.join(project_path, "partials", "_libjs.php")

    try:
        if not file_exists(path):
            logger.write(f"* {label} : FAIL")
            logger.write("    - partials/_libjs.php manquant")
            report.add(key, "BOOTSTRAP", label, False, "partials/_libjs.php manquant")
            return

        content = read_file(path)

        if contains_any(content, ["bootstrap.min.js"]):
            logger.write(f"* {label} : OK")
            report.add(key, "BOOTSTRAP", label, True)
        else:
            logger.write(f"* {label} : FAIL")
            logger.write("    - bootstrap.min.js absent de partials/_libjs.php")
            report.add(
                key,
                "BOOTSTRAP",
                label,
                False,
                "bootstrap.min.js absent de partials/_libjs.php"
            )

    except Exception as exc:
        logger.write(f"* {label} : FAIL")
        logger.write(f"    - erreur: {exc}")
        report.add(key, "BOOTSTRAP", label, False, str(exc))


def validate_bootstrap(project_path, logger, report):
    logger.write("## BOOTSTRAP")

    check_bootstrap_css_not_in_root_file(project_path, "index.php", logger, report)
    check_bootstrap_css_not_in_root_file(project_path, "view-user.php", logger, report)
    check_bootstrap_css_not_in_root_file(project_path, "view-admin.php", logger, report)

    check_bootstrap_js_not_in_root_file(project_path, "index.php", logger, report)
    check_bootstrap_js_not_in_root_file(project_path, "view-user.php", logger, report)
    check_bootstrap_js_not_in_root_file(project_path, "view-admin.php", logger, report)

    check_bootstrap_css_in_head(project_path, logger, report)
    check_bootstrap_js_in_libjs(project_path, logger, report)

    write_group_separator(logger)


def print_totals(logger, report):
    pts = report.data["totals"]["pts"]
    max_pts = report.data["totals"]["max"]

    percent = 0
    if max_pts > 0:
        percent = round((pts / max_pts) * 100)

    logger.write("")
    logger.write(f"TOTAL : {pts} / {max_pts} ({percent}%)")
    logger.write("")


def main():
    try:
        script_path = os.path.dirname(os.path.abspath(__file__))
        project_path = script_path
        student_folder = os.path.basename(project_path)

        report_path = os.path.join(script_path, REPORT_DIR)
        os.makedirs(report_path, exist_ok=True)

        txt_path = os.path.join(report_path, LIVRABLE_NAME + ".txt")
        json_path = os.path.join(report_path, LIVRABLE_NAME + ".json")

        logger = Logger()
        report = JsonReport(student_folder, project_path)

        logger.write("")
        logger.write("Validation automatique")
        logger.write(student_folder)
        logger.write("")

        validate_structure(project_path, logger, report)
        validate_php_structure(project_path, logger, report)
        validate_include_css(project_path, logger, report)
        validate_bootstrap(project_path, logger, report)

        print_totals(logger, report)

        logger.save_txt(txt_path)
        report.save(json_path)

        logger.write("Rapports générés :")
        logger.write(txt_path)
        logger.write(json_path)
        logger.write("")

    except Exception:
        print("ERREUR FATALE")
        traceback.print_exc()

    input("Appuyez sur Entrée pour quitter...")


if __name__ == "__main__":
    main()