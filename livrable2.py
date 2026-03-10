#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
==========================================================
Livrable2.py
Validation automatique - Structure du projet
==========================================================

Description
-----------
Script de correction automatique exécuté dans le dossier
de travail de l'étudiant.

Le script valide la structure du projet et génère :

_reports/Livrable2.txt
_reports/Livrable2.json

La sortie console est identique au fichier TXT.

Chaque critère vaut 1 point par défaut.

Auteur : AutoValidator
Date : 2026-03-10

Pré-requis
----------
Python 3.9+

Librairies utilisées (stdlib seulement)
---------------------------------------
os
json
datetime
traceback

Usage
-----
Double clic ou terminal :

python Livrable2.py
"""

import os
import json
import traceback
from datetime import datetime


# ==========================================================
# CONFIG
# ==========================================================

LIVRABLE_NAME = "Livrable2"
REPORT_DIR = "_reports"


# ==========================================================
# LOGGER
# ==========================================================

class Logger:

    def __init__(self):
        self.lines = []

    def write(self, text=""):
        print(text)
        self.lines.append(text)

    def save_txt(self, path):
        with open(path, "w", encoding="utf-8") as f:
            for line in self.lines:
                f.write(line + "\n")


# ==========================================================
# JSON BUILDER
# ==========================================================

class JsonReport:

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
        max_pts = 1

        self.data["criteria"][key] = {
            "group": group,
            "label": label,
            "pts": pts,
            "max": max_pts,
            "message": message
        }

        self.data["totals"]["pts"] += pts
        self.data["totals"]["max"] += max_pts

    def save(self, path):
        with open(path, "w", encoding="utf-8") as f:
            json.dump(self.data, f, indent=2, ensure_ascii=False)


# ==========================================================
# UTILS
# ==========================================================

def file_exists(path):
    return os.path.isfile(path)


def dir_exists(path):
    return os.path.isdir(path)


# ==========================================================
# VALIDATION GROUP 1
# ==========================================================

def validate_structure(project_path, logger, report):

    logger.write("## STRUCTURE")
    logger.write("Structure du projet")

    tests = [

        ("index.php", "index.php", "file"),
        ("view-user.php", "view-user.php", "file"),
        ("view-admin.php", "view-admin.php", "file"),

        ("data", "data", "dir"),

        ("partials/_head.php", "partials/_head.php", "file"),
        ("partials/_navbar.php", "partials/_navbar.php", "file"),
        ("partials/_footer.php", "partials/_footer.php", "file"),
        ("partials/_libjs.php", "partials/_libjs.php", "file"),

        ("src/JsonRepository.php", "src/JsonRepository.php", "file"),

        ("assets/css/styles.css", "assets/css/styles.css", "file"),
        ("assets/img", "assets/img", "dir"),
    ]

    for key, rel_path, typ in tests:

        full = os.path.join(project_path, rel_path)

        try:

            if typ == "file":
                ok = file_exists(full)
            else:
                ok = dir_exists(full)

            if ok:
                logger.write(f"* {rel_path} : OK")
                report.add(key, "GROUP1", rel_path, True)
            else:
                logger.write(f"* {rel_path} : FAIL")
                logger.write("    - manquant")
                report.add(key, "GROUP1", rel_path, False, "manquant")

        except Exception as e:

            logger.write(f"* {rel_path} : ERROR")
            logger.write(f"    - {e}")

            report.add(
                key,
                "GROUP1",
                rel_path,
                False,
                str(e)
            )

    logger.write("")
    logger.write("--------------")
    logger.write("")


def print_totals(logger, report):

    pts = report.data["totals"]["pts"]
    max_pts = report.data["totals"]["max"]

    percent = 0

    if max_pts > 0:
        percent = round((pts / max_pts) * 100)

    logger.write("")
    logger.write("TOTAL : "
                 + str(pts)
                 + " / "
                 + str(max_pts)
                 + " ("
                 + str(percent)
                 + "%)")
    logger.write("")

# ==========================================================
# MAIN
# ==========================================================

def main():

    try:

        # IMPORTANT
        # utiliser le dossier du script et non system32

        script_path = os.path.dirname(os.path.abspath(__file__))

        project_path = script_path
        student_folder = os.path.basename(project_path)

        report_path = os.path.join(script_path, REPORT_DIR)

        os.makedirs(report_path, exist_ok=True)

        txt_path = os.path.join(
            report_path,
            f"{LIVRABLE_NAME}.txt"
        )

        json_path = os.path.join(
            report_path,
            f"{LIVRABLE_NAME}.json"
        )

        logger = Logger()

        report = JsonReport(
            student_folder,
            project_path
        )

        logger.write("")
        logger.write("Validation automatique")
        logger.write(student_folder)
        logger.write("")

        validate_structure(
            project_path,
            logger,
            report
        )

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


# ==========================================================
# ENTRY
# ==========================================================

if __name__ == "__main__":
    main()